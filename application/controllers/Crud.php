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
            $this->data[' content'] = $message;
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
  if (empty($key)) {
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
   // make it into an associative array
      $codes[$code] = $category->name;
  $this->data['fcategory'] = makeCombobox('Category', 'category', $record->category,$codes);

  // show the editing form
  $this->data['pagebody'] = "maintenance-edit";
  $this->render();
  }

  public function save(){}
  public function cancel(){
        $this->session->unset_userdata('key');
        $this->session->unset_userdata('record');
        $this->index();
  }




}
