<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Padrinhos_model extends CI_Model {	

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	
	function getPermissoes()
	{
		$this->db->flush_cache();		
		
		$id_usuario = $this->encrypt->decode($this->session->userdata('lavie_id_usuario'));		
		$sql = "SELECT *
				FROM permissoes
				WHERE id_usuario = $id_usuario
			   ";		
		
		$query = $this->db->query($sql);		
		return $query->result();		
	}
	
	function inserirLogAcoes ($tabela, $acao, $sql)
	{
		$id_usuario = $this->encrypt->decode($this->session->userdata('lavie_id_usuario'));
		
		$data = array (
			'data_hora'  	=> date('Y-m-d H:i:s'),
			'id_usuario'    => $id_usuario,
			'tabela'    	=> $tabela,
			'acao'    		=> $acao,
			'sql'    		=> $sql,
			'ip'			=> $this->input->ip_address()
		);
		
		$this->db->set($data)->insert('logs_acoes');
		return $this->db->insert_id();
	}

	//=======================================================================
	//Outras Funções=========================================================
	//=======================================================================	
	
	function numPadrinhos ()
	{
		$this->db->select('*')->from('padrinhos');
		
		return $this->db->count_all_results();
	}
	
	function getPadrinhos ($offset = 0)
	{		
		$this->db->flush_cache();
		$this->db->select('*')->from('padrinhos')->order_by('nome', 'asc');
		
		return $this->db->get();
	}
	
	function getPadrinho ($id_padrinho)
	{		
		$where = array ('id_padrinho' => $id_padrinho);		
		$this->db->select('*')->from('padrinhos')->where($where);

		$query = $this->db->get();			
		return $query->row();
	}
	
	function setPadrinho ($data, $id_padrinho = "")
	{
		if ($id_padrinho)
		{
			$where = array ('id_padrinho' => $id_padrinho);
			$this->db->select('*')->from('padrinhos')->where($where);
			
			if ( ! $this->db->count_all_results())
			{
				throw new Exception('Acesso negado.');
			}
			else
			{
				$this->session->set_flashdata('resposta', 'Informações editadas com sucesso (:');
				
				$this->db->set($data);
                $this->db->where('id_padrinho', $id_padrinho);
                $this->db->update('padrinhos');
                
	             //Log Acesso
	            	$acao 		= "update";
	            	$tabela 	= "padrinhos";
	            	$sql 		= $this->db->last_query();
	            	$this->model->inserirLogAcoes($tabela, $acao, $sql);
	            //Log Acesso  
			}
			//exit;
			
			return $id_padrinho;
		}
		else
		{
			$this->db->set($data)->insert('padrinhos');
			
			$this->session->set_flashdata('resposta', 'Padrinho adicionado com sucesso (:');
			
	        //Log Acesso
	        	$acao 		= "insert";
	        	$tabela 	= "padrinhos";
	        	$sql 		= $this->db->last_query();
	        	$this->model->inserirLogAcoes($tabela, $acao, $sql);
	        //Log Acesso 			
			
			return $this->db->insert_id();
		}
	}
	
	function delPadrinho ($id_padrinho)
	{		
		$where = array ('id_padrinho' => $id_padrinho);
		$this->db->select('*')->from('padrinhos')->where($where);
		
		if ( ! $this->db->count_all_results())
		{
			throw new Exception('Acesso negado.');
		}
		else
		{
					
            $this->db->where('id_padrinho', $id_padrinho);
            $this->db->delete('padrinhos');
            
	        //Log Acesso
	        	$acao 		= "delete";
	        	$tabela 	= "padrinhos";
	        	$sql 		= $this->db->last_query();
	        	$this->model->inserirLogAcoes($tabela, $acao, $sql);
	        //Log Acesso 
	        
	        $this->session->set_flashdata('resposta', 'Padrinho excluído com sucesso (:');            
		}
	}
		
	function updateOrder ($ordem, $id)
	{
		$this->db->set('order', $ordem);
        $this->db->where('id_padrinho', $id);
        $this->db->update('padrinhos');
	}
}

/* End of file contatos_model.php */
/* Location: ./system/application/model/contatos_model.php */