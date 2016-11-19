<?php
/**
 * Created by PhpStorm.
 * User: Spencer
 * Date: 11/3/2016
 * Time: 5:03 PM
 */
class Crud extends Application {


    public function index()	{
        $role = $this->session->userdata('userrole');
        if ($role == 'user'){
            $message = "You are a user not an admin";
            $this->data['content'] = $message;
            $this->render('template');
            return;
        }

        $this->data['pagebody'] = 'maintenance';
        $this->data['items'] = $this->menu->all();
        $this->render();
    }

    public function edit($id=null) {
        // try the session first
        $key = $this->session->userdata('key');
        $record = $this->session->userdata('record');
        // if not there, get them from the database
//        if (empty($key)) {
//            $record = $this->menu->get($id);
//            $key = $id;
//            $this->session->set_userdata('key',$id);
//            $this->session->set_userdata('record',$record);
//        }

        if(empty($record)){
            $record = $this->menu->get($id);
            $key = $id;
            $this->session->set_userdata('key',$id);
            $this->session->set_userdata('record',$record);
        }

        //build the form fields
        $this->data['zsubmit'] = makeSubmitButton('Save', 'Submit Changes');

        $this->data['fid'] = makeTextField('Menu code', 'id', $record->id);
        $this->data['fname'] = makeTextField('Item name', 'name', $record->name);
        $this->data['fdescription'] = makeTextArea('Description', 'description', $record->description);
        $this->data['fprice'] = makeTextField('Price, each', 'price', $record->price);
        $this->data['fpicture'] = makeTextField('Item image', 'picture', $record->picture);
        $cats = $this->categories->all();
        // get an array of category objects
        foreach($cats as $code => $category)
          $codes[$category->id] = $category->name;
        $this->data['fcategory'] = makeCombobox('Category', 'category', $record->category,$codes);

        // show the editing form
        $this->data['pagebody'] = "maintenance-edit";
        $this->data['action'] = (empty($key)) ? 'Adding' : 'Editing';
        $this->show_any_errors();
        $this->render();
    }

    public function save(){
        $key = $this->session->userdata('key');
        $record = $this->session->userdata('record');

        if(empty($record)){
            $this->index();
            return;
        }

        $incoming = $this->input->post();
        foreach(get_object_vars($record) as $index => $value)
            if(isset($incoming[$index]))
                $record->$index = $incoming[$index];

        $newguy = $_FILES['replacement'];
        if(!empty($newguy['name]'])){
            $record->picture - $this->replace_picture();
            if($record->picture != null)
                $_POST['picture'];
        }

        $this->session->set_userdata('record', $record);

        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->menu->rules());
        if($this->form_validation->run() != TRUE)
            $this->error_messages = $this->form_validation->error_array();

        if($key == null)
            if($this->menu->exists($record->id))
                $this->error_messages[] = 'Duplicate key adding new menu item - not allowed';
        if(! $this->categories->exists($record->category))
            $this->error_messages[] = 'Invalid category code: ' . $record->category;

        if(! empty($this->error_messages)){
            $this->edit();
            return;
        }

        if($key == null){
            $this->menu->add($record);
        }
        else{
            $this->menu->update($record);
        }

        $this->index();
    }

    function cancel(){
        $this->session->unset_userdata('key');
        $this->session->unset_userdata('record');
        $this->index();
    }

    function delete(){
        $key = $this->session->userdata('key');
        $record = $this->session->userdata('record');

        if(!empty($record)){
            $this->menu->delete($key);
            $this->session->unset_userdata('key');
            $this->session->unset_userdata('record');
        }
        $this->index();
    }

    function add(){
        $key = NULL;
        $record = $this->menu->create();

        $this->session->set_userdata('key', $key);
        $this->session->set_userdata('record', $record);
        $this->edit();
    }

    function show_any_errors(){
        $result = '';
        if(empty($this->error_messages)){
            $this->data['error_messages'] = '';
            return;
        }

        foreach($this->error_messages as $onemessage)
            $result .= $onemessage . '<br/>';

        $this->data['error_messages'] = $this->parser->parse('maintenance-error', ['error_messages' => $result], true);

    }

    function replace_picture() {
        $config = [
            'upload_path' => './images', // relative to front controller
            'allowed_types' => 'gif|jpg|jpeg|png',
            'max_size' => 100, // 100KB should be enough for our graphical menu
            'max_width' => 256,
            'max_height' => 256, // actually, we want exactly 256x256
            'min_width' => 256,
            'min_height' => 256, // fixed it
            'remove_spaces' => TRUE, // eliminate any spaces in the name
            'overwrite' => TRUE, // overwrite existing image
        ];
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('replacement')) {
            $this->error_messages[] = $this->upload->display_errors();
            return NULL;
        }
        else
            return $this->upload->data('file_name');}


}
