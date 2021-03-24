<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller\Admin;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */

use App\Model\Entity\TBLMStaff;
use App\Model\Table\TBLTAreaStaffTable;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\I18n\Time;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

use Dompdf\Dompdf;

class StaffController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->loadModel('TBLMStaff');
        $this->loadModel('TBLMArea');
        $this->loadModel('TBLTAreaStaff');

        $this->viewBuilder()->setLayout('admin');
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);
    }

    /**
     *
     */
    public function index()
    {
        if ($this->getRequest()->is('ajax')) {
            $page = ($this->getRequest()->getData('start') / PAGE_LIMIT) + 1;
            $this->paginate = ['page' => $page, 'limit' => PAGE_LIMIT];

            $valueSearch = $this->getRequest()->getData('search.value');
            // search area
            $staffsValid = $this->TBLTAreaStaff->find()
                ->contain('TBLMArea')
                ->select(['TBLTAreaStaff.StaffID'])
                ->where(["TBLMArea.Name LIKE '%" .$valueSearch . "%' "])
                ->toArray() ;
            $arrStaffIdValid = [];
            foreach($staffsValid as $staff){
                array_push($arrStaffIdValid, $staff['StaffID']);
            }

            $like = "%{$valueSearch}%";
            $conditions['OR'] = [
                'StaffID LIKE' => (mb_detect_encoding($valueSearch) != "UTF-8") ? $like : "",
                'Name LIKE' => $like,
                'Position LIKE' => (mb_detect_encoding($valueSearch) != "UTF-8") ? $like : "",
                'Created_at LIKE' => (mb_detect_encoding($valueSearch) != "UTF-8") ? $like : "",
            ];

            if ($valueSearch != "" && !empty($arrStaffIdValid)) {
                $conditions['OR'] = [
                    'StaffID LIKE' => (mb_detect_encoding($valueSearch) != "UTF-8") ? $like : "",
                    'Name LIKE' => $like,
                    'Position LIKE' => (mb_detect_encoding($valueSearch) != "UTF-8") ? $like : "",
                    'Created_at LIKE' => (mb_detect_encoding($valueSearch) != "UTF-8") ? $like : "",
                    'StaffID IN' => $arrStaffIdValid
                ];
            }
            $conditions["FlagDelete ="] = 0;
            $orders = [
                "",
                "StaffID",
                "Name",
                "Password",
                "Position",
                "Area",
                "Region",
                "Created_at",
            ];
            $order = [
                $orders[$this->getRequest()->getData('order.0.column')] => $this->getRequest()->getData('order.0.dir')
            ];
            $query = $this->TBLMStaff->getStaffs($conditions, $order);

            $staffs = $this->paginate($query);
            $staffs = $staffs->toArray();

            $data = [];
            foreach($staffs as $staff){
                // AreaID
                $staff['AreaID'] = $staff['Area'];
                // Area Text
                $staff['Area'] = '';
                $areas = $this->TBLTAreaStaff->find()
                    ->contain('TBLMArea')
                    ->where(['StaffID' => $staff->StaffID])
                    ->order(['TBLMArea.Name'])
                    ->toArray();

                $last = (key(array_slice($areas, -1, 1, true)));
                foreach($areas as $index=>$area){
                    $area_name = $this->TBLMArea->find()->where(['AreaID' => $area->AreaID])->first();
                    if($index != $last){
                        $staff['Area'] .= $area_name->Name . ", ";
                    } else {
                        $staff['Area'] .= $area_name->Name;
                    }
                }
                array_push($data, $staff);
            }
            // sort area
            if($orders[$this->getRequest()->getData('order.0.column')] == "Area"){
                $area = array();
                foreach ($data as $key => $row)
                {
                    $area[$key] = $row['Area'];
                }
                if($this->getRequest()->getData('order.0.dir') == 'desc')
                    { array_multisort($area, SORT_DESC, $data); }
                else
                    { array_multisort($area, SORT_ASC, $data); }
            }

            $response = [
                'recordsTotal' => $query->count(),
                'recordsFiltered' => $query->count(),
                'data' => $data,
            ];
            return $this->responseJson($response);
        } else {
            $areas = $this->TBLMArea->find()->order(['Name' => 'asc']);
            $this->set('areas', $areas);
        }
    }

    /**
     *
     */
    public function edit()
    {
        if(false == $this->request->is('ajax')) {
            return $this->redirect(['controller' => 'Staff', 'action' => 'index']);
        }
        // get request obj
        $request = $this->getRequest();

        //get params
        $params = $request->getData() + $request->getQuery();
        // var_dump($params);
        // exit;


        $params['Created_at'] = date('Y-m-d H:i');

        if($params['Position'] == 'Area Leader' || $params['Position'] == 'Leader'){
            $this->TBLTAreaStaff->deleteAll(['StaffID' => $params['IDStaff']]);
            $areas = explode(",",$params['Areas']);
            foreach($areas as $item){
                $set = $this->TBLTAreaStaff->newEntity();
                $set->AreaID = $item;
                $set->StaffID = $params['IDStaff'];
                $this->TBLTAreaStaff->save($set);
            }
        }

        // clear image
        if($params['clearImage'] == "true"){
            $staff = $this->TBLMStaff->findById($params['ID'])->first();
            unlink(WWW_ROOT . "files/StaffImage/" . $staff->Image);
            $params['Image'] = "";
        }

        // save image
        $path = WWW_ROOT . "files/StaffImage";
        $result['success'] = 0;

        if (!empty($params['files'])) {
            if (!file_exists($path)) {
                mkdir($path);
            }
            $file = $params['files']; 
            foreach ($file as $item) {
                $ext = substr(strtolower(strrchr($item['name'], '.')), 1); //get the extension
                $arr_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'bmp']; //set allowed extensions
                $setNewFileName = $params['IDStaff'] . "_" . str_replace(" ","",$params['Name']) . "_" . date('Ymd', strtotime(Time::now())) . "." . $ext;

                if (in_array($ext, $arr_ext)) {
                    move_uploaded_file($item['tmp_name'], WWW_ROOT . '/files/StaffImage/' . $setNewFileName);
                    $setNewFileName = str_replace(' ', '', $setNewFileName);
                    $params['Image'] = $setNewFileName;
                }
            }
        }

        $entity = $this->TBLMStaff->findById($params['ID'])->first();

        $entity = $entity ? $entity : new TBLMStaff();

        $entity = $this->TBLMStaff->patchEntity($entity, $params, ['validate' => false]);

        $success = 1;
        $conn = ConnectionManager::get('default');
        try {
            $conn->begin();

            $save = $this->TBLMStaff->save($entity);

            if ($save) {
                $conn->commit();
            }
            else {
                throw new \Exception();
            }
        }
        catch (RecordNotFoundException $e) {
            $success = 0;
            $conn->rollback();
            print($e);
        }
        catch (\Exception $e) {
            $success = 0;
            $conn->rollback();
            print($e);
        }

        $response = [
            'status' => $success,
            'data' => $entity,
            'lst_staffs' => $this->TBLMStaff->getStaffs(),
        ];
        return $this->responseJson($response);
    }

    /**
     * @return \Cake\Http\Response
     */
    public function delete() {
        if(false == $this->request->is('ajax')) {
            return $this->redirect(['controller' => 'Staff', 'action' => 'index']);
        }
        // get request obj
        $request = $this->getRequest();

        //get params
        $id = $request->getData('id_staff');

        $entity = $this->TBLMStaff->findById($id)->first();

        $entity = $entity ? $entity : new TBLMStaff();
        $entity->FlagDelete = 1;
        $success = 1;

        try {
            $this->TBLMStaff->save($entity);
        }
        catch (RecordNotFoundException $e) {
            $success = 0;
            print($e);
        }
        catch (\Exception $e) {
            $success = 0;
            print($e);
        }

        $response = [
            'status' => $success,
            'lst_staffs' => $this->TBLMStaff->getStaffs(),
        ];
        return $this->responseJson($response);
    }

    /**
     *
     */
    public function search()
    {
        if(false == $this->request->is('ajax')) {
            return $this->redirect(['controller' => 'Staff', 'action' => 'index']);
        }
        // get request obj
        $request = $this->getRequest();

        $id = $request->getData('id_staff');
        $conditions["FlagDelete ="] = 0;
        $staff = $this->TBLMStaff->getStaff($id);
        $areas = $this->TBLTAreaStaff->find()->where(['StaffID' => $staff->StaffID]);
        $response = [
            'success' => 1,
            'data' => $staff,
            'areas' => $areas
        ];
        return $this->responseJson($response);
    }

    public function qrCode(){
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $success = false;

        $id = $this->getRequest()->getData('id');
        $data = $this->TBLMStaff->findById($id)->first();


        $qrCode = new QrCode(
'Staff ID: ' . $data->StaffID . '
' . 'Name: ' . $data->Name
            );
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM());

        $qrCode->setEncoding('ISO-8859-1');
        // Save it to a file
        $output = 'QRcode/tmp_'.$data['StaffID'].$data['Name'].".png";
        $qrCode->writeFile(WWW_ROOT.$output);
        $success = true;
        $response = [
            'success' => $success,
            'file' => $output,
            'data' => $data
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }

    public function saveQR(){
        $success = 0;
        $file = $this->getRequest()->getData('src');
        $target = str_replace("tmp_", "", $file);
        rename(WWW_ROOT . $file, WWW_ROOT . $target);
        $success = 1;
        $response = [
            'success' => $success
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }

    public function delQR(){
        $success = 0;
        $file = $this->getRequest()->getData('src');
        unlink(WWW_ROOT . $file);
        $success = 1;
        $response = [
            'success' => $success
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }

    public function exportNamecard()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $success = false;
        $id = $this->getRequest()->getData('id');
        $data = $this->TBLMStaff->findById($id)->first();

        // encode images
        $type_bg = pathinfo(WWW_ROOT . 'img/namecard/bg.png', PATHINFO_EXTENSION);
        $base64_bg = file_get_contents(WWW_ROOT . 'img/namecard/bg.png');
        $data['img_bg'] = 'data:image/' . $type_bg . ';base64,' . base64_encode($base64_bg);

        $type_logo = pathinfo(WWW_ROOT . 'img/namecard/logo.png', PATHINFO_EXTENSION);
        $base64_logo = file_get_contents(WWW_ROOT . 'img/namecard/logo.png');
        $data['img_logo'] = 'data:image/' . $type_logo . ';base64,' . base64_encode($base64_logo);

        $type_demo = pathinfo(WWW_ROOT . 'img/namecard/demo.jpg', PATHINFO_EXTENSION);
        $base64_demo = file_get_contents(WWW_ROOT . 'img/namecard/demo.jpg');
        $data['img_demo'] = 'data:image/' . $type_demo . ';base64,' . base64_encode($base64_demo);

        // qr code
        $qrCode = new QrCode(
'Staff ID: ' . $data->StaffID . '
' . 'Name: ' . $data->Name
            );
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM());

        $qrCode->setEncoding('ISO-8859-1');
        // Save it to a file
        $output = 'QRcode/'.$data['StaffID'].$data['Name'].".png";
        $qrCode->writeFile(WWW_ROOT.$output);

        $type_qr = pathinfo(WWW_ROOT . $output, PATHINFO_EXTENSION);
        $base64_qr = file_get_contents(WWW_ROOT . $output);
        $data['img_qr'] = 'data:image/' . $type_qr . ';base64,' . base64_encode($base64_qr);

        $builder = $this->viewBuilder();
        // configure as needed
        $builder->setLayout(false);
        $builder->setTemplate('/Element/Pdf/namecard');
        $builder->setHelpers(['Html']);
        // create a view instance
        $view = $builder->build(compact('data'));
        $content = $view->render();
        // echo $content; exit();

        $dompdf = new Dompdf();
        $dompdf->loadHtml(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), "UTF-8");
        $dompdf->set_option('defaultFont', 'Times-Roman');
        // $dompdf->set_base_path('/webroot/css/shift-leave-pdf.css');
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        /* save file */
        $fileDir = WWW_ROOT . "pdf";
        $path = $fileDir . "/namecard";
        if (!file_exists($path)) {
            mkdir($path);
        }
        $fileName = "Namecard_" . $data->StaffID . ".pdf";
        $output = $path . "/" . $fileName;

        $file = $dompdf->output();
        file_put_contents($output, $file);
        $success = true;
        $response = [
            'success' => $success,
            'file' => 'pdf/namecard/' . $fileName
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }

    public function sessionSort(){
        $col = $this->getRequest()->getData('col');
        $dir = $this->getRequest()->getData('dir');
        $this->request->session()->write('Config.sort.col', $col);
        $this->request->session()->write('Config.sort.dir', $dir);
        $response = [
            'result' => 'success',
        ];
        return $this->response->withType('application/json')->withStringBody(json_encode($response));
    }
}
