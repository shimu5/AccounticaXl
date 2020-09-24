<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Backups
 *
 * @author #Jakir Hosen Khan
 */

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Backups extends CrudeController {

    var $fileext = array('sql', 'txt', 'csv');

    public function anonList($request, $response) {
        $files = array();
        $backup_path = 'G:/xampp/htdocs';
        $dir = $backup_path . $this->c->Router->root . 'temp/backup';
        if (file_exists($dir)) {
            $handle = opendir($dir);
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && (is_dir($file) === false)) {
                        $files[] = $file;
                    }
                }
                closedir($handle);
            }
        } else {
            //$this->setFlash('backup/sql folder doesn\'t exists.');
        }
        $response->set('files', $files);
    }

    public function anonBackup($request, $response) {
        global $DATABASE;
        $default = 'default';
        $db = $DATABASE[DATABASE_SET][$default];
        
        $path = 'D:/xampp/mysql/bin/';
        $backup_path = 'D:/webroot/accounticaxl/0.1/dev'.'/temp/downloads/backup/';

        $dbname = $db['name'];
        $dbhost = $db['host'];
        $dbuser = $db['user'];
        $dbpass = $db['pass'];
        $backupFile = $backup_path . $dbname . '_' . date("Y_m_d_H_i_s") . '.sql';

        $command = "{$path}mysqldump -h $dbhost -u $dbuser --password=$dbpass $dbname --add-drop-table --ignore-table=$dbname.admins --ignore-table=$dbname.auth_sessions > $backupFile \n\n";

        /* execute file dumping command */
        system($command);
        
        if(isset($request->get['auth_login']) && $request->get['auth_login'] == 0){
            $BP = new \scheduler\components\BProcessor();
            $BP->set_process_status(array(array('job_history_id'=>$request->get['job_history_id'])), \scheduler\ProcessStatus::$DONE, 'Done Successfully');
            die;
        }else{
            $url = $this->c->Router->root . 'index.php/accountica/backups/list';
            redirect($url);
        }
    }

    public function anonDownload($request, $response) {
        list($filename) = $request->params(Null);
        if (!empty($filename)) {
            $path = 'G:/xampp/mysql/bin/';
            $dir = 'G:/xampp/htdocs' . $this->c->Router->root . 'temp/backup/';
            $backupFile = $dir . $filename;

            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $myFile = $backupFile;
            $fh = fopen($myFile, 'r');
            $theData = fread($fh, filesize($myFile));
            fclose($fh);
            echo $theData;
            die;
        } else {
            //$this->setFlash('Error');
            echo'Not download';
            die;
        }
    }

    public function anonDelete($request, $response) {
        list($filename) = $request->params(Null);
        if (!empty($filename)) {
            $dir = 'G:/xampp/htdocs' . $this->c->Router->root . 'temp/backup/';
            $filename = $dir . $filename;
            if (is_file($filename)) {
                if (@unlink($filename) === true) {
                    echo 'Successfully Deleted!';
                    die;
                    //$this->setFlash('Successfully deleted.');
                } else {
                    echo 'Cannot be deleted';
                    die;
                    //$this->setFlash('Cannot be deleted.');
                }
            } else {
                echo 'No file exists';
                die;
                //$this->setFlash('No file exists.');
            }
            $url = $this->c->Router->root . 'index.php/accountica/backups/list';
            redirect($url);
        } else {
            //$this->setFlash('Error');
        }
    }

    public function anonRestore($request, $response) {
        list($filename) = $request->params(Null);
        if (!empty($filename)) {
            if ($this->c->Auth->user('id') == 1) {
                global $DATABASE;
                $default = 'default';
                $db = $DATABASE[DATABASE_SET][$default];
//        $path = MYSQL_PATH;
                $path = 'G:/xampp/mysql/bin/';
                $backup_path = 'G:/xampp/htdocs';

                $dbname = $db['name'];
                $dbhost = $db['host'];
                $dbuser = $db['user'];
                $dbpass = $db['pass'];
                $dumpFile = $backup_path . $this->c->Router->root . 'temp/backup/' . $filename;
                if (file_exists($dumpFile)) {
                    $command = "{$path}mysql -h $dbhost -u $dbuser --password=$dbpass $dbname < $dumpFile \n\n";
                    system($command);
                    echo 'Database is restored';
                    die;
                    //$this->setFlash('Database is restored.');
                } else {
                    echo 'No file specified';
                    die;
                    //$this->setFlash('No file specified.');
                }
            } else {
                echo 'Something is really wrong';
                die;
                //$this->setFlash('Something is really wrong.');
            }
            $url = $this->c->Router->root . 'index.php/accountica/backups/list';
            redirect($url);
        } else {
            echo 'No file specified';
            die;
        }
    }

    public function anonUpload($request,$response){
        //pr($request);die;
        if(!empty($this->files)){
            pr($this->files);die;
            $type = $this->files['Backup']['name']['type'];
            $name = $this->files['Backup']['name']['name'];
            $size = $this->files['Backup']['name']['size'];
            $tmp = $this->files['Backup']['name']['tmp_name'];
            $error = $this->files['Backup']['name']['error'];

            if( ( $type == 'text/plain' || $type == 'application/octet-stream' ) && $this->__extension($name)=== true ){
                if($error > 0){
                    $this->setFlash('Error:'.$error);
                }else{
                    $dir = $this->Backup->getDir();
                    $filename = $dir.$name;
                    if(file_exists($filename)){
                        $this->setFlash('File Already Exists.');
                    }else{
                        move_uploaded_file($tmp,$filename);
                        $this->setFlash('Successfully Uploaded.');
                        $this->redirect(array('action' => 'list'));
                    }
                }
            }else{
                $this->setFlash('File extensions must be '.implode(',',$this->fileext));

            }
        }
    }

}

?>
