<?php
/**
 * ICalService.php
 * Génère un fichier iCalendar (.ics) pour un rendez-vous
 * Compatible : Google Calendar, Outlook, Apple Calendar
 *
 * À placer dans : Services/ICalService.php
 */

namespace Services;

class ICalService
{
    /**
     * Génère le contenu du fichier .ics pour un RDV
     *
     * @param array $rdv     Ligne rendez_vous avec medecin_nom / medecin_prenom
     * @param array $medecin Tableau ['nom', 'prenom', 'specialite'] du médecin
     * @return string        Contenu brut du fichier iCal
     */
    public static function generer(array $rdv, array $medecin): string
    {
        // ── Fuseau horaire du projet ─────────────────────────────────────────
        $timezone = 'Africa/Tunis'; // UTC+1 — adapte si besoin

        // ── Sécurisation des données d'entrée ────────────────────────────────
        $dateRdv  = trim($rdv['date_rdv']  ?? '');
        $heureRdv = trim($rdv['heure_rdv'] ?? '00:00:00');
        // Normaliser l'heure : accepte "HH:MM" et "HH:MM:SS"
        if (strlen($heureRdv) === 5) $heureRdv .= ':00';

        // ── Calcul des dates UTC ─────────────────────────────────────────────
        try {
            $dtStart = new \DateTime(
                $dateRdv . 'T' . $heureRdv,
                new \DateTimeZone($timezone)
            );
        } catch (\Exception $e) {
            // Fallback : maintenant + 1h si la date est invalide
            $dtStart = new \DateTime('now', new \DateTimeZone($timezone));
        }
        $dtEnd = clone $dtStart;
        $dtEnd->modify('+30 minutes');

        $dtStart->setTimezone(new \DateTimeZone('UTC'));
        $dtEnd->setTimezone(new \DateTimeZone('UTC'));

        $startStr = $dtStart->format('Ymd\\THis\\Z');
        $endStr   = $dtEnd->format('Ymd\\THis\\Z');
        $stampStr = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Ymd\\THis\\Z');

        // ── Métadonnées ──────────────────────────────────────────────────────
        $rdvId      = $rdv['id'] ?? uniqid();
        $uid        = 'rdv-' . $rdvId . '-' . md5($rdvId . $dateRdv) . '@mediflow';
        $patientNom = trim(($rdv['patient_prenom'] ?? '') . ' ' . ($rdv['patient_nom'] ?? ''));
        $drNom      = 'Dr. ' . trim(($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? ''));
        $specialite = $medecin['specialite'] ?? '';

        $summary     = self::escapeIcal("RDV - " . $drNom);
        $description = self::escapeIcal(
            "Rendez-vous avec " . $drNom .
            ($specialite ? " (" . $specialite . ")" : '') .
            "\nPatient : " . $patientNom .
            "\nReference : #" . $rdvId .
            "\nMerci de vous presenter 10 minutes avant votre rendez-vous."
        );
        $location = self::escapeIcal('MediFlow Clinique');

        // ── Construction RFC 5545 ────────────────────────────────────────────
        // Chaque propriété sur sa propre ligne, CRLF obligatoire
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//MediFlow//RDV//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:MediFlow',
            'X-WR-TIMEZONE:' . $timezone,
            'BEGIN:VEVENT',
            'UID:'     . $uid,
            'DTSTAMP:' . $stampStr,
            'DTSTART:' . $startStr,
            'DTEND:'   . $endStr,
            'SUMMARY:' . $summary,
            'LOCATION:' . $location,
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
        ];

        // DESCRIPTION : fold si > 75 chars (RFC 5545 §3.1)
        $descLine = 'DESCRIPTION:' . $description;
        if (mb_strlen($descLine) > 75) {
            $lines[] = self::foldLine($descLine);
        } else {
            $lines[] = $descLine;
        }

        // Alarme rappel 1h avant
        $lines[] = 'BEGIN:VALARM';
        $lines[] = 'TRIGGER:-PT1H';
        $lines[] = 'ACTION:DISPLAY';
        $lines[] = 'DESCRIPTION:Rappel RDV - ' . $summary;
        $lines[] = 'END:VALARM';
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';
        $lines[] = ''; // ligne vide finale obligatoire

        return implode("\r\n", $lines);
    }

    /**
     * Envoie directement le fichier .ics au navigateur (force le téléchargement)
     */
    public static function telecharger(array $rdv, array $medecin): void
    {
        $contenu  = self::generer($rdv, $medecin);
        $filename = 'rdv-mediflow-' . $rdv['id'] . '.ics';

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($contenu));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $contenu;
        exit;
    }

    // ── Helpers RFC 5545 ─────────────────────────────────────────────────────

    /** Échappe les caractères spéciaux iCal */
    private static function escapeIcal(string $str): string
    {
        $str = str_replace('\\', '\\\\', $str);
        $str = str_replace(';', '\\;', $str);
        $str = str_replace(',', '\\,', $str);
        // Les retours à la ligne dans DESCRIPTION → \n iCal
        $str = str_replace(["\r\n", "\r", "\n"], '\\n', $str);
        return $str;
    }

    /**
     * Plie une ligne à 75 octets max (RFC 5545 §3.1)
     * Les lignes de continuation commencent par un espace
     */
    private static function foldLine(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }
        $folded = '';
        while (strlen($line) > 75) {
            $folded .= substr($line, 0, 75) . "\r\n ";
            $line = substr($line, 75);
        }
        return $folded . $line;
    }
}