<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Form\Galeria\CreateForm;

class IndexController extends AbstractActionController {

    private function getPublicImageDir() {
        return '/uploads';
    }
    
    private function getImageDir() {
        return getcwd() . '/public' . $this->getPublicImageDir();
    }
    
    public function indexAction() {
        $request = $this->getRequest();
        $deleteParam = $request->getQuery('delete');
        $error = null;
        $info = null;
        if ($deleteParam){
            //kontrola spravnosti suboru
            if (preg_match("/^[a-z0-9_]+\\.[a-z]+$/i", $deleteParam) > 0){
                $path = "{$this->getImageDir()}/{$deleteParam}";
                if (file_exists($path)){
                    unlink($path);
                    $info = "Obrázok bol vymazaný";
                } else {
                    $error = "Nesprávny parameter delete - súbor neexistuje";
                }
            } else {
                $error = "Nesprávny parameter delete";
            }
        }
        
        $images = array_diff(scandir($this->getImageDir()), array('.', '..'));
        usort($images, function($a, $b) {
            return filemtime("{$this->getImageDir()}/{$b}") - filemtime("{$this->getImageDir()}/{$a}");
        });
        return new ViewModel([
            'imageDir' => $this->getPublicImageDir(),
            'images' => $images,
            'error' => $error,
            'info' => $info
        ]);
    }

    public function addGalleryAction() {
        $request = $this->getRequest();
        
        // assuming $captcha is an instance of some Laminas\Captcha\AdapterInterface:
        $form = new CreateForm($this->getImageDir());

        // If the form doesn't define an input filter by default, inject one.
        //$form->setInputFilter(new Contact\ContactFilter());
       

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);

            // Validate the form
            if ($form->isValid()) {
                $validatedData = $form->getData();
                //todo:
                $this->redirect()->toRoute('home');
            } else {
                $messages = $form->getMessages();
                //print_r($messages);
                //Array ( [csrf] => Array ( [isEmpty] => Value is required and can't be empty ) )
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

}
