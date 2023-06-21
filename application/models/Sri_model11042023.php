<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sri_model extends CI_Model {

	public function update_factura($id,$data){
		$this->db->where('fac_id',$id);
		return $this->db->update("erp_factura",$data);
			
	}

	public function update_nota($id,$data){
		$this->db->where('ncr_id',$id);
		return $this->db->update("erp_nota_credito",$data);
			
	}

	public function update_guia($id,$data){
		$this->db->where('gui_id',$id);
		return $this->db->update("erp_guia_remision",$data);
			
	}

	public function update_retencion($id,$data){
		$this->db->where('ret_id',$id);
		return $this->db->update("erp_retencion",$data);
			
	}

	public function update_liquidacion($id,$data){
		$this->db->where('reg_id',$id);
		return $this->db->update("erp_reg_documentos",$data);
			
	}
	

	public function facturas_no_enviadas(){
		$sql="(select fac_id, fac_clave_acceso, 1 as tipo from erp_factura where fac_estado=4 order by random() limit 1)
				union
				(select ncr_id, ncr_clave_acceso, 2 as tipo from erp_nota_credito where ncr_estado=4 order by random() limit 1)
				union
				(select gui_id, gui_clave_acceso, 3 as tipo from erp_guia_remision where gui_estado=4 order by random() limit 1)
				union 
				(select ret_id, ret_clave_acceso, 4 as tipo from erp_retencion where ret_estado=4 order by random() limit 1)
				union
				(select reg_id, reg_clave_acceso, 5 as tipo from erp_reg_documentos where reg_estado=4 and reg_tipo_documento='3' and reg_clave_acceso!='' order by random() limit 1)";
		$resultado = $this->db->query($sql);
        return $resultado->row();
			
	}
	
}

?>