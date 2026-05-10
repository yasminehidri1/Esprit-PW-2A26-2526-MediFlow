<?php
/**
 * OpenRouter Chatbot API Endpoint
 * Secure proxy for OpenRouter API requests (using Google Gamma model)
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ob_start();

header('Content-Type: application/json');

try {
    session_start();
    
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }
    
    // Get request body
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['message'])) {
        throw new Exception('No message provided');
    }
    
    $userMessage = trim($input['message']);
    if (strlen($userMessage) < 1 || strlen($userMessage) > 2000) {
        throw new Exception('Message must be 1-2000 characters');
    }
    
    // Load config
    require_once __DIR__ . '/../config.php';
    $apiKey = \config::getOpenRouterApiKey();
    
    if (empty($apiKey)) {
        throw new Exception('OpenRouter API key not configured. Please add your API key to config.php');
    }
    
    // Helper function to build role-specific prompts
    function buildRoleSpecificPrompt($role) {
        $baseInfo = "You are MediFlow Assistant, a helpful AI for the MediFlow healthcare platform. 

ABOUT MEDIFLOW:
MediFlow is a comprehensive healthcare management system that offers:
- Patient Profile Management: Users can create and manage their medical profiles with profile pictures
- Equipment Rental: Patients can browse and rent medical equipment
- Doctor Appointments: Schedule and manage appointments with doctors
- Medical Records: Access and manage digital medical documents
- Prescriptions: View and manage prescriptions from doctors
- Stock Management: Inventory tracking for medical equipment and medications";
        
        // Role-specific instructions
        $roleSpecific = match($role) {
            'Patient' => "
YOUR ROLE: You are an assistant specifically for PATIENTS.

ONLY answer questions about:
- How to rent medical equipment
- How to schedule appointments with doctors
- How to manage their profile and upload profile pictures
- How to view their prescriptions
- How to check their reservation history
- General information about using the patient features

DO NOT provide information about:
- Admin functions
- Staff management
- Equipment inventory (technical side)
- User management
- Stock management

If asked about non-patient features, politely say: 'That feature is only available for staff members. I can help you with patient features instead.'",
            
            'Médecin' => "
YOUR ROLE: You are an assistant specifically for DOCTORS.

ONLY answer questions about:
- Managing patient consultations
- Writing and managing prescriptions
- Viewing patient medical records
- Scheduling appointments
- Patient management
- Medical documentation

DO NOT provide information about:
- Equipment rental (patient feature)
- Admin functions
- Stock management
- User management

If asked about non-doctor features, politely say: 'That feature is not available for your role. I can help you with doctor-related tasks instead.'",
            
            'Technicien' => "
YOUR ROLE: You are an assistant specifically for EQUIPMENT MANAGERS.

ONLY answer questions about:
- Managing equipment inventory
- Tracking equipment reservations
- Equipment maintenance
- Equipment availability
- Rental status management
- Stock levels

DO NOT provide information about:
- Patient features
- Doctor functions
- Admin functions
- User management
- Medical records

If asked about non-equipment features, politely say: 'That feature is not related to equipment management. I can help you with inventory and equipment tasks instead.'",
            
            'Admin' => "
YOUR ROLE: You are an assistant specifically for ADMINISTRATORS.

ONLY answer questions about:
- User management (adding, editing users)
- System settings
- Dashboard statistics
- Role management
- System overview
- General admin functions

DO NOT provide specific patient or medical advice. Direct healthcare questions to appropriate staff.

If asked about specific medical questions, say: 'For medical questions, please consult with qualified medical professionals on the platform.'",
            
            default => "
YOUR ROLE: You are an assistant for MediFlow users.

ONLY answer questions about MediFlow features relevant to the user's role.

If asked about features outside your scope, politely redirect the user."
        };
        
        return $baseInfo . $roleSpecific . "

Keep responses concise and helpful. Be professional and friendly.";
    }
    
    // OpenRouter API endpoint
    $url = 'https://openrouter.ai/api/v1/chat/completions';
    
    // Get user role from session
    $userRole = $_SESSION['user']['role'] ?? 'Patient';
    
    // Create role-specific system prompt
    $systemPrompt = buildRoleSpecificPrompt($userRole);
    
    $payload = [
        'model' => 'openrouter/free',
        'messages' => [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 1024,
        'top_p' => 0.95,
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,  // Increased to 60 seconds
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'HTTP-Referer: http://localhost/integration',
            'X-Title: MediFlow'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER => false,  // Disabled for local testing
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
    ]);
    
    error_log('Sending request to OpenRouter: ' . $url);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrNo = curl_errno($ch);
    curl_close($ch);
    
    error_log('cURL Response - Code: ' . $httpCode . ', Error: ' . $curlError);
    
    if ($curlErrNo !== 0) {
        throw new Exception('API request failed (Code ' . $curlErrNo . '): ' . $curlError);
    }
    
    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? ($errorData['error'] ?? 'Unknown error');
        throw new Exception('OpenRouter API error: ' . $errorMsg);
    }
    
    $data = json_decode($response, true);
    
    // Log response for debugging
    error_log('OpenRouter Response: ' . json_encode($data, JSON_PRETTY_PRINT));
    
    // Extract reply with fallback handling
    $reply = null;
    
    if (!empty($data['choices'][0]['message']['content'])) {
        $reply = $data['choices'][0]['message']['content'];
    } elseif (!empty($data['result']['outputs'][0]['text'])) {
        $reply = $data['result']['outputs'][0]['text'];
    } elseif (!empty($data['text'])) {
        $reply = $data['text'];
    }
    
    if (empty($reply) || trim($reply) === '') {
        error_log('Invalid response structure: ' . print_r($data, true));
        throw new Exception('Invalid API response: No content in message');
    }
    
    $reply = trim($reply);
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
