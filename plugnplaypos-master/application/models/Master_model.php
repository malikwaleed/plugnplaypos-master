<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Master_model
 *
 * @author user
 */
class Master_model extends CI_Model {

    public function generateIngredientCode() {
        $company_id = $this->session->userdata('company_id');
        $ingredient_count = $this->db->query("SELECT count(id) as ingredient_count
               FROM tbl_ingredients where company_id='$company_id'")->row('ingredient_count');
        $ingredient_code = str_pad($ingredient_count + 1, 3, '0', STR_PAD_LEFT);
        return $ingredient_code;
    }

    public function generateFoodMenuCode() {
        $company_id = $this->session->userdata('company_id');
        $food_menu_count = $this->db->query("SELECT count(id) as food_menu_count
             FROM tbl_food_menus where company_id='$company_id'")->row('food_menu_count');
        $food_menu_code = str_pad($food_menu_count + 1, 3, '0', STR_PAD_LEFT);
        return $food_menu_code;
    }

    public function getIngredientListWithUnit($company_id) {
        $result = $this->db->query("SELECT tbl_ingredients.id, tbl_ingredients.name, tbl_ingredients.code, tbl_units.unit_name 
          FROM tbl_ingredients 
          JOIN tbl_units ON tbl_ingredients.unit_id = tbl_units.id
          WHERE tbl_ingredients.company_id=$company_id AND tbl_ingredients.del_status = 'Live'  
          ORDER BY tbl_ingredients.name ASC")->result();
        return $result;
    }

    public function getFoodMenuIngredients($id) {
        $this->db->select("*");
        $this->db->from("tbl_food_menus_ingredients");
        $this->db->order_by('id', 'ASC');
        $this->db->where("food_menu_id", $id);
        $this->db->where("del_status", 'Live');
        return $this->db->get()->result();
    }

    public function getModifierIngredients($id) {
        $this->db->select("*");
        $this->db->from("tbl_modifier_ingredients");
        $this->db->order_by('id', 'ASC');
        $this->db->where("modifier_id", $id);
        $this->db->where("del_status", 'Live');
        return $this->db->get()->result();
    }

    public function getFoodMenuModifiers($id) {
        $this->db->select("*");
        $this->db->from("tbl_food_menus_modifiers");
        $this->db->order_by('id', 'ASC');
        $this->db->where("food_menu_id", $id);
        $this->db->where("del_status", 'Live');
        return $this->db->get()->result();
    }

    public function mulit_featured_photo($width = '', $sessionName = '') {
        $config['upload_path'] = './assets/uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = '2048';
        $config['encrypt_name'] = TRUE;
        $config['detect_mime'] = TRUE;
        $this->load->library('upload', $config);
        $this->load->library('image_lib', $config);
        if ($this->upload->do_upload("featured_photo")) {
            $upload_info = $this->upload->data();
            $pc_thumb = $upload_info['file_name'];
            $config['image_library'] = 'gd2';
            $config['source_image'] = './assets/uploads/' . $pc_thumb;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $this->image_lib->clear();
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->session->set_userdata($sessionName, $pc_thumb);
        }
    }
    
    public function getImageUploadConfiguaration(){
        // $config['upload_path']       =       $this->gallery_path.'/upload';
        $config['upload_path']      =       realpath(APPPATH.'../assets').'POS';
        $config['allowed_types']    =       'gif|png|jpg|jpeg';
        $config['max_size']         =       '6000';
        $config['max_width']        =       '0';
        $config['max_height']       =       '0';
        return $config;
    }

    public function uploadImage($config,$photoFieldName=''){
        $this->load->library('upload');
        $this->upload->initialize($config);
        $photoFieldName=($photoFieldName!='')?$photoFieldName:'photo';
        if(!$this->upload->do_upload($photoFieldName)){
            $error      =       array(
                'error'     =>      $this->upload->display_error()
            );
            echo $error['error'];
        }else{
            return $this->upload->data();           
        }
        // $image_data=$this->upload->data();
    }

    public function getFoodTags($company_id) {
        $this->db->select("*");
        $this->db->from("tbl_food_tags");
        $this->db->order_by('id', 'ASC');
        $this->db->where("company_id", $company_id);

        return $this->db->get()->result();
    }

    public function deleteFoodTag($tag_id) {
        $this->db->where('id', $tag_id);
        $this->db->delete('tbl_food_tags');
    }

    public function getItemsByTag($id) {
        $this->db->select("*");
        $this->db->from("tbl_food_tags_items");
        $this->db->order_by('id', 'ASC');
        $this->db->where("tag_id", $id);
        return $this->db->get()->result();
    }

    public function getFoodMenuTags($food_menu_id) {
        $this->db->select("*");
        $this->db->from("tbl_food_menus_tags");
        $this->db->order_by('id', 'ASC');
        $this->db->where("food_menu_id", $food_menu_id);;
        return $this->db->get()->result();
    }

    public function getIPPMethods($company_id) {
        $result = $this->db->query("SELECT * 
          FROM tbl_ipp_methods
          WHERE company_id=$company_id AND del_status = 'Live'  
          ORDER BY id")->result();
        return $result;
    }

    public function getIPPMethodById($id, $company_id) {
        $result = $this->db->query("SELECT * 
          FROM tbl_ipp_methods
          WHERE id=$id AND company_id=$company_id AND del_status = 'Live'")->row();
        return $result;
    }
}
