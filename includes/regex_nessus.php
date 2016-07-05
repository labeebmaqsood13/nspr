<?php

class Regex{

	public $policy_name 		  	  = '/(?<=(policyName>)).*(?=<)/';
	public $host_end 				  = '/(?<=("HOST_END">)).*(?=<)/';
	public $patch_summary_total_cves  = '/(?<=("patch-summary-total-cves">)).*(?=<)/';
	public $cpe                       = '/(?<=("cpe">)).*(?=<)/';
	public $os 						  = '/(?<=("operating-system">)).*(?=<)/';
	public $host_ip		  			  = '/(?<=("host-ip">)).*(?=<)/';
	public $netbios_name			  = '/(?<=("netbios-name">)).*(?=<)/';
	public $host_start 				  = '/(?<=("HOST_START">)).*(?=<)/';
	public $host_fqdn				  = '/(?<=("host-fqdn">)).*(?=<)/';
	public $mac 					  = '/(?<=("mac-address">)).*(?=<)/';
	public $table					  = "#(?<=(<ReportItem)).*(?=>)#";

	public function policy_name($reading){

		preg_match("$this->policy_name",$reading,$policy_name);
		
		return $policy_name[0];
	}


	public function host_end($reading){

		preg_match("$this->host_end",$reading,$host_end);
		return $host_end[0];
	}	


	public function patch_summary_total_cves($reading){

		preg_match("$this->patch_summary_total_cves",$reading,$patch_summary_total_cves);
		return $patch_summary_total_cves[0];
	}		

	public function cpe($reading){

		preg_match("$this->cpe",$reading,$cpe);

		return $cpe[0];
	}

	public function os($reading){

		preg_match("$this->os",$reading,$os);

		return $os[0];
	}

	public function host_ip($reading){

		preg_match("$this->host_ip",$reading,$host_ip);

		return $host_ip[0];
	}

	public function netbios_name($reading){

		preg_match("$this->netbios_name",$reading,$netbios_name);

		return $netbios_name[0];
	}
	
	public function host_start($reading){

		preg_match("$this->host_start",$reading,$host_start);

		return $host_start[0];
	}	

	public function host_fqdn($reading){

		preg_match("$this->host_fqdn",$reading,$host_fqdn);

		return $host_fqdn[0];
	}		

	public function mac($reading){

		preg_match("$this->mac",$reading,$mac);

		return $mac[0];
	}			

	public function table($reading){

		preg_match_all("$this->table",$reading,$table);

		return $table[0];
	}			

}

$regex = new Regex();

?>
