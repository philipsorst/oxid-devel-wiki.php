<?php

namespace Dontdrinkandroot\OxidDevelWikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->redirectToRoute('ddr_gitki_directory', ['path' => '/']);
    }
}
