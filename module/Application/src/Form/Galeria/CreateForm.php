<?php

declare (strict_types=1);

namespace Application\Form\Galeria;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\InputFilter;

class CreateForm extends Form {

    private $imageDir;

    public function __construct($imageDir) {
        parent::__construct('add_gallery');
        //echo 'image dir='; print_r($this->imageDir);
        $this->setAttribute('method', 'post');
        $this->imageDir = $imageDir;
        $this->addElements();
        $this->addInputFilter();
    }

    // This method adds elements to form.
    protected function addElements() {

        #name element
        /*
        $this->add([
            'type' => Element\Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Názov:'
            ],
            'attributes' => [
                'required' => true,
                'size' => 40,
                'maxlength' => 200,
                'pattern' => '^[a-zA-Z0-9]+$', //enforcing what type of data we accept
                'data-toggle' => 'toooltip',
                'class' => 'form-control', //styling the textfield
                'title' => 'Názov môže mať iba písmená A-Z a číslice',
                'placeholder' => 'Zadajte názov'
            ]
        ]);
        */

        // File Input
        $file = new Element\File('image');
        $file->setLabel('Foto:');
        $file->setAttribute('id', 'image');
        $file->setAttribute('required', true);
        $this->add($file);

        #cross-site request-forgery (csrf) field
        $this->add([
            'type' => Element\Csrf::class,
            'name' => 'csrf',
            'options' => [
                'csrf_options' => ['timeout' => 600]
            ]
        ]);

        #submit button
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'create_button',
            'attributes' => [
                'value' => 'Pridať foto',
                'class' => 'tbn btn-primary'
            ]
        ]);
    }

    private function addInputFilter() {
        $inputFilter = new InputFilter\InputFilter();

        // File Input
        $fileInput = new InputFilter\FileInput('image');
        $fileInput->setRequired(true);

        // Define validators and filters as if only one file was being uploaded.
        // All files will be run through the same validators and filters
        // automatically.
        $fileInput->getValidatorChain()
                ->attachByName('filesize', ['max' => 204800])
                ->attachByName('filemimetype', ['mimeType' => 'image/png,image/x-png,image/jpg,image/jpeg'])
                ->attachByName('fileimagesize', ['maxWidth' => 1000, 'maxHeight' => 1000]);


        if (!file_exists($this->imageDir)) {
            mkdir($this->imageDir, 0755, true);
        }
        // All files will be renamed, e.g.:
        //   ./data/tmpuploads/foto_4b3403665fea6.png,
        //   ./data/tmpuploads/foto_5c45147660fb7.png
        $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                [
                    //'overwrite' => true,
                    'target' => $this->imageDir . '/foto.png',
                    'randomize' => true,
                    'use_upload_extension' => true
                ]
        );
        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }

}
