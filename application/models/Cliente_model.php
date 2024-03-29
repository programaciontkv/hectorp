<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente_model extends CI_Model {

	public function lista_clientes(){
		$this->db->select('c.*,e.est_descripcion'); 
		$this->db->from('erp_i_cliente c'); 
		$this->db->join('erp_estados e','e.est_id=c.cli_estado'); 
		$this->db->order_by('cli_raz_social', 'asc'); 
		$resultado=$this->db->get();
		return $resultado->result();
			
	}

	public function lista_secuencial_cliente($sig){
		$this->db->like('cli_codigo',$sig); 
		$this->db->order_by('cli_codigo', 'desc'); 
		$resultado=$this->db->get('erp_i_cliente');
		return $resultado->row();
			
	}	

	public function lista_un_cliente($id){
		$this->db->select('c.*,e.est_descripcion'); 
		$this->db->from('erp_i_cliente c'); 
		$this->db->join('erp_estados e','e.est_id=c.cli_estado'); 
		$this->db->where("cli_id=$id OR cli_ced_ruc='$id'", null);
		$resultado=$this->db->get();
		return $resultado->row();
			
	}

	public function lista_un_cliente_cedula($id){
		$this->db->select('c.*,e.est_descripcion'); 
		$this->db->from('erp_i_cliente c'); 
		$this->db->join('erp_estados e','e.est_id=c.cli_estado'); 
		$this->db->where("cli_ced_ruc='$id'", null);
		$resultado=$this->db->get();
		return $resultado->row();
			
	}
	
	public function lista_un_cliente2($txt){
		$txt = strtoupper($txt);
		$this->db->select('cli_ced_ruc,cli_raz_social'); 
		$this->db->from('erp_i_cliente'); 
		$this->db->where("cli_raz_social LIKE '%$txt%' OR cli_ced_ruc LIKE '%$txt%' ", null);
		$this->db->where("cli_estado ", 1);
		$resultado=$this->db->get();
		return $resultado->result();		
			
	}

	public function lista_un_cliente3($txt){
		$txt = strtoupper($txt);
		$this->db->select('cli_ced_ruc,cli_raz_social'); 
		$this->db->from('erp_i_cliente'); 
		$this->db->where("cli_parroquia LIKE '%$txt%' or cli_calle_prin LIKE '%$txt%' ", null);
		$this->db->where("cli_estado ", 1);
		$resultado=$this->db->get();
		return $resultado->result();		
			
	}

	public function insert($data){
		
		return $this->db->insert("erp_i_cliente",$data);
			
	}

	public function update($id,$data){
		$this->db->where('cli_id',$id);
		return $this->db->update("erp_i_cliente",$data);
			
	}

	public function delete($id){
		$this->db->where('cli_id',$id);
		return $this->db->delete("erp_i_cliente");
			
	}

	public function lista_clientes_estado($est){
		$this->db->where('cli_estado',$est); 
		$this->db->order_by('cli_raz_social', 'asc'); 
		$resultado=$this->db->get('erp_i_cliente');
		return $resultado->result();
			
	}
	public function lista_clientes_buscador($text,$estado){
		$this->db->select('c.*,e.est_descripcion');
		$this->db->where("(c.cli_raz_social like '%$text%' or c.cli_ced_ruc like '%$text%' or cli_parroquia LIKE '%$text%' or cli_calle_prin LIKE '%$text%') ",null);
		if ($estado != "") {
			$this->db->where("cli_estado",$estado);
		}
		$this->db->from('erp_i_cliente c'); 
		$this->db->join('erp_estados e','e.est_id=c.cli_estado'); 
		$this->db->order_by('cli_raz_social', 'asc'); 
		$resultado=$this->db->get();
		return $resultado->result();
			
	}

	public function lista_clientes_buscador_2($text,$estado){
		$this->db->select('c.*,e.est_descripcion');
		$this->db->where("(c.cli_raz_social like '%$text%' or c.cli_ced_ruc like '%$text%' or cli_parroquia LIKE '%$text%' or cli_calle_prin LIKE '%$text%') ",null);
		if ($estado != "") {
			$this->db->where("cli_estado",$estado);
		}
		$this->db->from('erp_i_cliente c'); 
		$this->db->join('erp_estados e','e.est_id=c.cli_estado'); 
		$this->db->order_by('cli_raz_social', 'asc');
		$this->db->limit(600);
		$resultado=$this->db->get();
		return $resultado->result();
			
	}

	public function lista_facturas_cliente($id){
        $query ="select fac_id,cli_id, fac_numero,fac_total_valor,fac_fecha_emision, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id group by fac_id,cli_id, fac_numero,fac_total_valor, fac_fecha_emision ORDER BY fac_numero ASC ";
        $resultado=$this->db->query($query);
		return $resultado->result();
		//echo $this->db->last_query();
    }
}

?>