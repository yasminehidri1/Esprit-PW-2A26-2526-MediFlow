<?php
namespace Services;

class ClaudeService {
    private string $apiKey;
    private array  $models = [
        'openrouter/owl-alpha',
        'mistralai/mistral-7b-instruct:free',
        'meta-llama/llama-3.2-3b-instruct:free',
    ];
    private string $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct() {
        $this->apiKey = \config::getClaudeApiKey();
    }

    /**
     * Analyse une description de demande d'ordonnance et retourne
     * le niveau d'urgence détecté + une justification courte.
     *
     * @return array{urgence: string, justification: string}
     */
    public function analyzeUrgency(string $description, string $patientUrgence = 'normale'): array {
        $prompt = "Tu es un assistant médical expert. Analyse cette demande d'ordonnance et détermine le niveau d'urgence médicale réel.\n\n"
                . "Description du patient : " . $description . "\n"
                . "Urgence déclarée par le patient : " . $patientUrgence . "\n\n"
                . "Réponds UNIQUEMENT avec un objet JSON valide sur une seule ligne, sans markdown ni explication :\n"
                . '{"urgence":"normale","justification":"..."}'
                . "\n\nValeurs possibles pour urgence : normale, urgent, tres_urgent";

        try {
            $raw  = $this->call($prompt, 150);
            $start = strpos($raw, '{');
            $end   = strrpos($raw, '}');
            if ($start !== false && $end !== false) {
                $raw = substr($raw, $start, $end - $start + 1);
            }
            $data = json_decode($raw, true) ?? [];

            $valid   = ['normale', 'urgent', 'tres_urgent'];
            $urgence = in_array($data['urgence'] ?? '', $valid, true)
                       ? $data['urgence']
                       : $patientUrgence;

            return [
                'urgence'       => $urgence,
                'justification' => substr(trim($data['justification'] ?? 'Analyse automatique.'), 0, 200),
            ];
        } catch (\Throwable) {
            return ['urgence' => $patientUrgence, 'justification' => ''];
        }
    }

    /**
     * Génère un message de refus professionnel et bienveillant.
     */
    public function generateRefusMessage(string $raison, string $description, int $variation = 1): string {
        $variationNote = $variation > 1
            ? "\n\nATTENTION : Génère une formulation ENTIÈREMENT DIFFÉRENTE de la précédente. Change la structure des phrases, le vocabulaire et l'angle d'approche. Variation n°{$variation}."
            : '';

        $prompt = "Tu es un assistant médical. Rédige un message de refus professionnel, empathique et bienveillant en français pour une demande d'ordonnance.\n\n"
                . "Motif du refus : " . $raison . "\n"
                . "Description de la demande : " . $description . "\n\n"
                . "Contraintes :\n"
                . "- 3 à 4 phrases maximum\n"
                . "- Ton professionnel et humain\n"
                . "- Proposer une alternative ou prochaine étape si possible\n"
                . "- Ne pas mentionner de noms propres\n"
                . "- Commencer par 'Madame, Monsieur,'\n\n"
                . "Réponds uniquement avec le message, sans titre ni guillemets."
                . $variationNote;

        $temperature = $variation > 1 ? 0.9 : 0.4;

        try {
            return trim($this->call($prompt, 350, $temperature));
        } catch (\Throwable) {
            return "Madame, Monsieur, votre demande d'ordonnance n'a pas pu être traitée : " . $raison . ". Nous vous invitons à prendre rendez-vous pour en discuter avec votre médecin.";
        }
    }

    /**
     * Analyse des symptômes et retourne une évaluation d'urgence + conseils.
     * Ne pose JAMAIS de diagnostic — triage uniquement.
     *
     * @return array{urgence: string, conseil: string, signes_alerte: array, message: string}
     */
    /**
     * Analyse des symptômes : essaie l'IA, bascule sur mots-clés si indisponible.
     *
     * @return array{urgence: string, conseil: string, signes_alerte: array, message: string}
     */
    public function analyzeSymptoms(string $symptoms): array {
        $prompt = "Tu es un assistant médical de triage. Évalue UNIQUEMENT l'urgence des symptômes décrits, sans poser de diagnostic.\n\n"
                . "Symptômes : " . $symptoms . "\n\n"
                . "Réponds avec EXACTEMENT ce format JSON sur une seule ligne, sans markdown ni texte autour :\n"
                . "{\"urgence\":\"NIVEAU\",\"conseil\":\"ACTION_COURTE\",\"signes_alerte\":\"SIGNE1, SIGNE2\",\"message\":\"REPONSE_COMPLETE\"}\n\n"
                . "NIVEAU = non_urgent | semi_urgent | urgent | urgence_vitale\n"
                . "ACTION_COURTE = max 80 caractères\n"
                . "SIGNES_ALERTE = 2 signes d'aggravation séparés par virgule\n"
                . "REPONSE_COMPLETE = réponse empathique max 200 caractères, sans diagnostic";

        try {
            $raw = $this->call($prompt, 400, 0.2);

            $start = strpos($raw, '{');
            $end   = strrpos($raw, '}');
            if ($start !== false && $end !== false) {
                $raw = substr($raw, $start, $end - $start + 1);
            }

            $data    = json_decode($raw, true) ?? [];
            $valid   = ['non_urgent', 'semi_urgent', 'urgent', 'urgence_vitale'];
            $urgence = in_array($data['urgence'] ?? '', $valid, true) ? $data['urgence'] : 'semi_urgent';

            $signesRaw = $data['signes_alerte'] ?? '';
            $signes    = is_array($signesRaw)
                ? array_values(array_slice($signesRaw, 0, 3))
                : array_values(array_filter(array_map('trim', explode(',', (string)$signesRaw))));

            $conseil = trim($data['conseil'] ?? '');
            $message = trim($data['message'] ?? '');

            if (empty($conseil) || empty($message)) {
                throw new \RuntimeException('Réponse IA incomplète');
            }

            return [
                'urgence'       => $urgence,
                'conseil'       => substr($conseil, 0, 100),
                'signes_alerte' => array_slice($signes, 0, 3),
                'message'       => substr($message, 0, 300),
            ];
        } catch (\Throwable $e) {
            error_log('[ChatbotIA] API indisponible, bascule mots-clés : ' . $e->getMessage());
            return $this->analyzeSymptomsByKeywords($symptoms);
        }
    }

    /** Triage par mots-clés — fonctionne sans API. */
    private function analyzeSymptomsByKeywords(string $symptoms): array {
        // Normalisation : minuscules + suppression accents
        $t = mb_strtolower($symptoms, 'UTF-8');
        $t = strtr($t, [
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','à'=>'a','â'=>'a','ä'=>'a',
            'î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','œ'=>'oe','æ'=>'ae',
        ]);
        $has = fn(string $kw): bool => str_contains($t, $kw);

        // ── URGENCE VITALE ───────────────────────────────────────────
        $vitale = [
            ['thoracique'], ['poitrine'], ['oppression'], ['serrement cœur'],
            ['infarctus'], ['crise cardiaque'],
            ['bras gauche'], ['machoire'],
            ['mal a respirer'], ['difficul', 'respir'],
            ['essoufflement'],
            ['perte de connaissance'], ['inconscient'], ['evanoui'],
            ['convulsion'], ['epilepsie'],
            ['paralysie'], ['paralys'],
            ['avc'], ['accident vasculaire'],
            ['gonflement gorge'], ['choc anaphylactique'],
            ['raideur nuque'], ['nuque raide'],
            ['ne respire plus'], ['arret cardiaque'],
        ];
        foreach ($vitale as $pattern) {
            $ok = true;
            foreach ($pattern as $kw) { if (!$has($kw)) { $ok = false; break; } }
            if ($ok) return [
                'urgence'       => 'urgence_vitale',
                'conseil'       => 'Appelez le 15 (SAMU) ou le 18 immédiatement',
                'signes_alerte' => ['Aggravation rapide', 'Perte de connaissance'],
                'message'       => 'Vos symptômes peuvent indiquer une urgence médicale grave. Appelez le 15 (SAMU) immédiatement ou demandez à être conduit aux urgences.',
            ];
        }

        // ── URGENT ───────────────────────────────────────────────────
        $urgent = [
            'forte douleur','douleur intense','insupportable','atroce',
            'fievre 39','fievre 40','39.','39,','40 degre','39 degre',
            'vomissements repetit','vomit sans arret',
            'douleur abdominale','appendicite','abdomin',
            'traumatisme cranien','coup a la tete',
            'fracture','os casse','luxation',
            'brulure','hemorragie','saignement important',
            'allergie severe','urticaire generalis',
            'infection uri','cystite',
            'depuis 6 heure','depuis 8 heure','depuis 12','depuis 24',
        ];
        foreach ($urgent as $kw) {
            if ($has($kw)) return [
                'urgence'       => 'urgent',
                'conseil'       => 'Consultez un médecin ou les urgences aujourd\'hui',
                'signes_alerte' => ['Fièvre qui dépasse 40°C', 'Aggravation rapide des douleurs'],
                'message'       => 'Vos symptômes nécessitent une consultation médicale aujourd\'hui. Contactez votre médecin ou rendez-vous aux urgences si la douleur s\'intensifie.',
            ];
        }

        // ── SEMI_URGENT ───────────────────────────────────────────────
        $semi = [
            'fievre','temperature elevee','38','chaud',
            'courbature','fatigue','epuise',
            'mal a la gorge','gorge','angine','pharyngite',
            'toux','bronchite','sinusite',
            'migraine','mal a la tete','maux de tete','cephale',
            'nausee','vomissement','diarrhee','gastro',
            'infection','plaie','blessure',
            'mal au dos','lombaire','douleur dos',
            'otite','oreille','mal aux oreilles',
            'gonflement','rougeur','eruption',
            'depuis hier','depuis 2 jour','depuis 3 jour','depuis plusieurs',
            'douleur',
        ];
        foreach ($semi as $kw) {
            if ($has($kw)) return [
                'urgence'       => 'semi_urgent',
                'conseil'       => 'Consultez votre médecin dans les 24 à 48 heures',
                'signes_alerte' => ['Fièvre qui dépasse 39°C', 'Symptômes qui s\'aggravent'],
                'message'       => 'Vos symptômes méritent une attention médicale prochaine. Prenez rendez-vous avec votre médecin dans les 48h, ou plus tôt si cela s\'aggrave.',
            ];
        }

        // ── NON URGENT (défaut) ───────────────────────────────────────
        return [
            'urgence'       => 'non_urgent',
            'conseil'       => 'Prenez rendez-vous avec votre médecin prochainement',
            'signes_alerte' => ['Apparition de fièvre', 'Aggravation des symptômes'],
            'message'       => 'Vos symptômes ne semblent pas urgents. Consultez votre médecin lors de votre prochain rendez-vous si les symptômes persistent ou s\'aggravent.',
        ];
    }

    private function call(string $prompt, int $maxTokens, float $temperature = 0.3): string {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Clé API OpenRouter non configurée.');
        }

        $lastError = '';
        foreach ($this->models as $model) {
            $payload = json_encode([
                'model'       => $model,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => $maxTokens,
                'temperature' => $temperature,
            ]);

            $ch = curl_init($this->apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKey,
                    'HTTP-Referer: http://localhost/integration',
                    'X-Title: MediFlow',
                ],
            ]);

            $body   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($body !== false && $status === 200) {
                $resp = json_decode($body, true);
                $text = $resp['choices'][0]['message']['content'] ?? '';
                if (!empty($text)) return $text;
            }

            $lastError = 'OpenRouter [' . $model . '] HTTP ' . $status . ' — ' . $body;
            error_log('[ClaudeService] ' . $lastError);
        }

        throw new \RuntimeException($lastError);
    }
}
