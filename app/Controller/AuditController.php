<?php

App::uses('AppController', 'Controller');
App::uses('FirePHP', 'Vendor');

class AuditController extends AppController {

    /**
     * Name of the controller 
     */
    public $name = 'Audit';

    /**
     * Models used by the controler 
     */
    public $uses = array('Audit','User','University','Data');

    /**
     * The components used in this controller 
     */
    public $components = array('Session', 'URLCheck');

    /**
     * Displays the list of audits
     */
    public function index() {
        // Gets the session information
        //$access = $this->Session->read('access');
        //$before = memory_get_usage();        
        // Gets all the indicators
        $audit = $this->Audit->find('all', array('fields'=>array('Audit.idauditoria',
            'Audit.valor', 'Audit.anho', 'Audit.usuario_idusuario', 'dato_iddato',
            'Data.nombre', 'Data.descripcion')));
        //$audit = $this->Audit->find('all');
        //FirePHP::getInstance(true)->log($audit);
        /*$after = memory_get_usage();
        $allocatedSize = ($after - $before);
        FirePHP::getInstance(true)->log($audit);
        FirePHP::getInstance(true)->log($allocatedSize);*/
        // Sets the list of indicators
        //$before1 = memory_get_usage();
        $this->set('audit', $audit);
        /*$after1 = memory_get_usage();
        $allocatedSize1 = ($after1 - $before1);
        FirePHP::getInstance(true)->log($allocatedSize1);*/
        // Renders the list
        $this->render('index', 'conare');
    }
    
    /**
     * Gets the audit information
     */
    public function get_audit() {
        // The result
        $result = array();

        // Indicates error
        $result['success'] = false;

        // Gets the id
        $auditId = $this->request->data['auditId'];
        $audit = $this->Audit->findByidauditoria($auditId);
        $user = $this->User->findByidusuario($audit['Audit']['usuario_idusuario']);
        $universidad = $this->University->findByiduniversidad($user['User']['universidad_iduniversidad']);
        
        $dato = $this->Data->findByiddato($audit['Audit']['dato_iddato']);
        
        if($user){
            // Saves the info to display about audit
            $result['nombre'] = $user['User']['nombre'];
            $result['apellido'] = $user['User']['apellido'];
            $result['universidad'] = $universidad['University']['acronimo'];
        }else{
            // Set values to null if user was not found or deleted
            $result['nombre'] = 'Usuario no encontrado/borrado';
            $result['apellido'] = '';
            $result['universidad'] = '---';
        }
        
        $result['valor'] = $audit['Audit']['valor'];
        $result['anho'] = $audit['Audit']['anho'];
        $result['dato'] = $dato['Data']['nombre'];
        $result['descripcionDato'] = $dato['Data']['descripcion']; 
        $result['fecha'] = $audit['Audit']['fecha'];
        $result['success'] = true;
        // Sends the response
        $this->response->type('application/json');
        $this->response->body(json_encode($result));
        return $this->response;
    }

}



