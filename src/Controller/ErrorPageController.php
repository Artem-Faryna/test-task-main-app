<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorPageController extends AbstractController
{
    #[Route('/error-page', name: 'error_page')]
    public function index(): Response
    {
        return $this->render('errorPage/error_page.html.twig');
    }
}
