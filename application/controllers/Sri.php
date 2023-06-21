<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
set_time_limit(0);

class Sri extends CI_Controller {

    function __construct(){
        parent:: __construct();
        if(!$this->session->userdata('s_login')){
            redirect(base_url());
        }
        $this->load->model('configuracion_model');
        $this->load->library("nusoap_lib");
        $this->load->model('sri_model');
    }
    
    
    public function documentos_no_enviados(){
        
        $factura=$this->sri_model->facturas_no_enviadas();
        if(!empty($factura->fac_id)){
            $this->envio_sri($factura->fac_clave_acceso,$factura->fac_id,$factura->tipo);
        }
        
    }
	
	public function envio_sri($clave,$id,$tipo){
        $amb=$this->configuracion_model->lista_una_configuracion('5');
        $ambiente=$amb->con_valor;

        if($ambiente!=0){
            
            set_time_limit(0);
             if ($ambiente == 2) { //Produccion
                $client = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
            } else {      //Pruebas
                $client = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
            }
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = FALSE;

            // Calls
            $result = $client->call('autorizacionComprobante', ["claveAccesoComprobante" => $clave]);
            
            if (empty($result['RespuestaAutorizacionComprobante']['autorizaciones'])) {
               $this->enviar_xml($clave); 
            } else {
                $res = $result['RespuestaAutorizacionComprobante']['autorizaciones']['autorizacion'];
                if($res['estado']!='AUTORIZADO'){

                    $this->enviar_xml($clave); 
                }else{
                    if($tipo==1){
                        $data = array(
                                        'fac_autorizacion'=>$res['numeroAutorizacion'], 
                                        'fac_fec_hora_aut'=>$res['fechaAutorizacion'], 
                                        'fac_xml_doc'=>$res['comprobante'], 
                                        'fac_estado'=>'6'
                                    );
                        $this->sri_model->update_factura($id,$data);
                    }else if($tipo==2){
                        $data = array(
                                        'ncr_autorizacion'=>$res['numeroAutorizacion'], 
                                        'ncr_fec_hora_aut'=>$res['fechaAutorizacion'], 
                                        'ncr_xml_doc'=>$res['comprobante'], 
                                        'ncr_estado'=>'6'
                                    );
                        $this->sri_model->update_nota($id,$data);
                    }else if($tipo==3){
                        $data = array(
                                        'gui_autorizacion'=>$res['numeroAutorizacion'], 
                                        'gui_fec_hora_aut'=>$res['fechaAutorizacion'], 
                                        'gui_xml_doc'=>$res['comprobante'], 
                                        'gui_estado'=>'6'
                                    );
                        $this->sri_model->update_guia($id,$data);
                    }else if($tipo==4){
                        $data = array(
                                        'ret_autorizacion'=>$res['numeroAutorizacion'], 
                                        'ret_fec_hora_aut'=>$res['fechaAutorizacion'], 
                                        'ret_xml_doc'=>$res['comprobante'], 
                                        'ret_estado'=>'6'
                                    );
                        $this->sri_model->update_retencion($id,$data);
                    }else if($tipo==5){
                        $data = array(
                                        'reg_num_autorizacion'=>$res['numeroAutorizacion'], 
                                        'reg_fec_hora_aut'=>$res['fechaAutorizacion'], 
                                        'reg_xml_doc'=>$res['comprobante'], 
                                        'reg_estado'=>'6'
                                    );
                        $this->sri_model->update_liquidacion($id,$data);
                    }
                }
            }
        }    

    }

    public function enviar_xml($clave){
        // if(file_exists($clave.'.xml')) {
            $progr=$this->configuracion_model->lista_una_configuracion('15');
            $programa=$progr->con_valor2;
            $credencial=$this->configuracion_model->lista_una_configuracion('13');
            $cred=explode('&',$credencial->con_valor2);
            $firma=$cred[2];
            $pass=$cred[1];
            $amb=$this->configuracion_model->lista_una_configuracion('5');
            $ambiente=$amb->con_valor;
            $direccion=$this->configuracion_model->lista_una_configuracion('23');
            $ip=$direccion->con_valor2;
            header("Location: http://$ip/central_xml/envio_sri/firmar.php?clave=$clave&programa=$programa&firma=$firma&password=$pass&ambiente=$ambiente");
        // }
        
    }

    
}
?>