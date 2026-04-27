<?php
/**
 * MediFlow - Point d'Entrée Principal
 * Front Controller Pattern
 * 
 * @package MediFlow
 * @version 1.0.0
 */

// Démarrer la session
session_start();

// Inclure la configuration
require_once 'config.php';

// Inclure les Models
require_once 'Models/Product.php';
require_once 'Models/Order.php';

// Inclure les Controllers
require_once 'Controllers/ProductController.php';
require_once 'Controllers/OrderController.php';
require_once 'Controllers/FrontProductController.php';
require_once 'Controllers/FrontOrderController.php';
require_once 'Controllers/SupplierProductController.php';
require_once 'Controllers/SupplierOrderController.php';

// Routage simple
$action = isset($_GET['action']) ? strip_tags($_GET['action']) : 'products';
$method = isset($_GET['method']) ? strip_tags($_GET['method']) : 'list';

try {
    switch ($action) {
        case 'products':
            $controller = new ProductController();
            
            switch ($method) {
                case 'list':
                    $controller->list();
                    break;
                    
                case 'search':
                    $controller->search();
                    break;
                    
                case 'filter':
                    $controller->filter();
                    break;
                    
                case 'create':
                    $controller->create();
                    break;
                    
                case 'store':
                    $controller->store();
                    break;
                    
                case 'edit':
                    $controller->edit();
                    break;
                    
                case 'update':
                    $controller->update();
                    break;
                    
                case 'delete':
                    $controller->delete();
                    break;
                    
                case 'updateStock':
                    $controller->updateStock();
                    break;
                    
                default:
                    $controller->list();
            }
            break;
            
        case 'cart':
            // Afficher la page du panier
            require_once 'views/Back/cart.php';
            break;
            
        case 'orders':
            $controller = new OrderController();
            
            switch ($method) {
                case 'create':
                    $controller->create();
                    break;
                    
                case 'update':
                    $controller->update();
                    break;
                    
                case 'list':
                    $controller->list();
                    break;
                    
                case 'view':
                    $controller->view();
                    break;
                    
                case 'validate':
                    $controller->validate();
                    break;
                    
                default:
                    $controller->list();
            }
            break;
        
        case 'front':
            // Front Office - Détermine le contrôleur en fonction de la méthode
            $controller_name = isset($_GET['controller']) ? strip_tags($_GET['controller']) : 'products';
            
            switch ($controller_name) {
                case 'products':
                    $controller = new FrontProductController();
                    
                    switch ($method) {
                        case 'list':
                            $controller->list();
                            break;
                        
                        case 'view':
                            $controller->view();
                            break;
                        
                        default:
                            $controller->list();
                    }
                    break;
                
                case 'orders':
                    $controller = new FrontOrderController();
                    
                    switch ($method) {
                        case 'list':
                            $controller->list();
                            break;
                        
                        case 'view':
                            $controller->view();
                            break;
                        
                        default:
                            $controller->list();
                    }
                    break;
                
                default:
                    $controller = new FrontProductController();
                    $controller->list();
            }
            break;
        
        case 'supplier':
            // BackOffice Fournisseur - Détermine le contrôleur
            $controller_name = isset($_GET['controller']) ? strip_tags($_GET['controller']) : 'products';
            
            switch ($controller_name) {
                case 'products':
                    $controller = new SupplierProductController();
                    
                    switch ($method) {
                        case 'list':
                            $controller->list();
                            break;
                        
                        case 'search':
                            $controller->search();
                            break;
                        
                        case 'filter':
                            $controller->filter();
                            break;
                        
                        case 'create':
                            $controller->create();
                            break;
                        
                        case 'store':
                            $controller->store();
                            break;
                        
                        case 'edit':
                            $controller->edit();
                            break;
                        
                        case 'update':
                            $controller->update();
                            break;
                        
                        case 'delete':
                            $controller->delete();
                            break;
                        
                        case 'updateStock':
                            $controller->updateStock();
                            break;
                        
                        default:
                            $controller->list();
                    }
                    break;
                
                case 'orders':
                    $controller = new SupplierOrderController();
                    
                    switch ($method) {
                        case 'list':
                            $controller->list();
                            break;
                        
                        case 'view':
                            $controller->view();
                            break;
                        
                        default:
                            $controller->list();
                    }
                    break;
                
                default:
                    $controller = new SupplierProductController();
                    $controller->list();
            }
            break;
            
        default:
            // Redirection par défaut
            header('Location: ?action=products&method=list');
            exit;
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
    exit;
}
?>
