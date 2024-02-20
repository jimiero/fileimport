<?php

namespace App\Controller;

use App\Entity\Deposer;
use App\Entity\Persona;
use App\Form\PersonaType;
use App\Repository\DeposerRepository;
use App\Repository\PersonaRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class UploadController extends AbstractController
{
    /**
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @return Response
     */
    #[Route('/doUpload', name: 'do-upload')]
    public function index(Request $request, string $uploadDir,
                          FileUploader $uploader, LoggerInterface $logger, EntityManagerInterface $em): Response
    {
          $token = $request->get("token");

        if (!$this->isCsrfTokenValid('upload', $token))
        {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed",  Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/plain']);
        }

        $file = $request->files->get('file');


        if (empty($file))
        {
            return new Response("No file specified",
                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }

        $filename = $file->getClientOriginalName();
        $uploadedFile = $uploader->upload($uploadDir, $file, $filename);


        $deposer = new Deposer();
        $deposer->setExt($uploadedFile->getClientOriginalExtension())
                ->setNom($uploadedFile->getClientOriginalName())
                ->setRoute($uploadDir.'/'.$uploadedFile->getClientOriginalName());
        $em->persist($deposer);
        $em->flush();


        $spreadsheet = IOFactory::load($uploadDir.'/'.$deposer->getNom());

        $spreadData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true,);

        $header = array_shift($spreadData);

        foreach ($spreadData as $entry) {
            $existingEntry = $em->getRepository(Persona::class)->findOneBy(['NomDuGroupe' => $entry['B']]);

            if (!$existingEntry) {
                $new = new Persona();
                $new->setNomDuGroupe($entry['A'])
                    ->setOrigine($entry['B'])
                    ->setVille($entry['C'])
                    ->setAnneeDebut($entry['D'])
                    ->setAnneeSeparation($entry['E'])
                    ->setFodateurs($entry['F'])
                    ->setMembers($entry['G'])
                    ->setCourantMusical($entry['H'])
                    ->setPresentation($entry['I'])
                    ->setDeposer($deposer);
                $em->persist($new);

            }
        }

        $em->flush();

        return $this->redirectToRoute('app_home_index');
    }

    #[Route('/import/excel/{deposer}', name: 'my-import-excel')]
    public function import(Deposer $deposer, EntityManagerInterface $em, $uploadDir): Response
    {

        $spreadsheet = IOFactory::load($uploadDir.'/'.$deposer->getNom());

        $spreadData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true,);
        $header = array_shift($spreadData);

        /** @var Persona $persona */
        return $this->render('home/render.html.twig', [
            'spreadData' => $spreadData,
            'personas' => $deposer->getPersonas(),
            'header' => $header,

        ]);
    }


    #[Route('/import/excel/{deposer}', name: 'persona_edit')]
    public function edit(Request $request, Persona $persona, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PersonaType::class, $persona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_home_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('persona/edit.html.twig', [
            'persona' => $persona,
            'form' => $form,
        ]);
    }

    #[Route('/api/excel/filesList', name: 'list_fichers')]
    public function listDeFichers(DeposerRepository $deposerRepository): Response
    {
        $query = $deposerRepository->findby([], array('id'=>'desc'));

        $data = [];

        foreach ( $query as $deposer) {
            $data[] = [
                'id' => $deposer->getId(),
                'name' => $deposer->getNom(),
                'ext' => $deposer->getExt(),
            ];
        }



        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/excel/listPersonas', name: 'list_personas')]
    public function listFichers(Request $request, PersonaRepository $personaRepository, $uploadDir): Response
    {
        $personas = $personaRepository->findby([], array('id'=>'desc'));

        $data = [];

        $data['headers'] = [
            'A' => "Nom du groupe",
            'B' => "Origine",
            'C' => "Ville",
            'D' => "Année début",
            'E' => "Année séparation",
            'F' => "Fondateurs",
            'G' => "Membres",
            'H' => "Courant musical",
            'I' => "Présentation",
        ];

        foreach ($personas as $persona) {
            $spreadsheet = IOFactory::load($uploadDir.'/'.$persona->getDeposer()->getNom());

            $spreadData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true,);

            $headers = array_shift($spreadData);

            $newHeaders = [
                'A' => 'nomeDuGroupe',
                'B' => 'origine',
                'C' => 'ville',
                'D' => 'anne',
                'E' => 'separation',
                'F' => 'fondateurs',
                'G' => 'memberes',
                'H' => 'courant',
                'I' => 'presentation'
            ];

            $data[] = [
                'headers' => $headers,
                'updatedHeaders' => $newHeaders,
                'id' => $persona->getId(),
                'deposer' => $persona->getDeposer(),
                'nomeDuGroupe' => $persona->getNomDuGroupe(),
                'origine' => $persona->getOrigine(),
                'ville' => $persona->getVille(),
                'anne' => $persona->getAnneeDebut(),
                'separation' => $persona->getAnneeSeparation(),
                'fondateurs' => $persona->getFodateurs(),
                'memberes' => $persona->getMembers(),
                'courant' => $persona->getCourantMusical(),
                'presentation' => $persona->getPresentation(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/excel/listPersona/{deposer}', name: 'list_persona')]
    public function listPersona(Request $request, Deposer $deposer, DeposerRepository $deposerRepository, PersonaRepository $personaRepository, $uploadDir): Response
    {
        $data = [];

        $personas = $deposer->getPersonas();

        $spreadsheet = IOFactory::load($uploadDir . '/' . $deposer->getNom());

        $spreadData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true,);

        $headers = array_shift($spreadData);

        $data['headers'] = $headers;

        foreach ($personas as $persona) {
            $newHeaders = [
                'A' => 'nomeDuGroupe',
                'B' => 'origine',
                'C' => 'ville',
                'D' => 'anne',
                'E' => 'separation',
                'F' => 'fondateurs',
                'G' => 'memberes',
                'H' => 'courant',
                'I' => 'presentation'
            ];

            $data[] = [
                'updatedHeaders' => $newHeaders,
                'id' => $persona->getId(),
                'deposer' => $persona->getDeposer(),
                'nomeDuGroupe' => $persona->getNomDuGroupe(),
                'origine' => $persona->getOrigine(),
                'ville' => $persona->getVille(),
                'anne' => $persona->getAnneeDebut(),
                'separation' => $persona->getAnneeSeparation(),
                'fondateurs' => $persona->getFodateurs(),
                'memberes' => $persona->getMembers(),
                'courant' => $persona->getCourantMusical(),
                'presentation' => $persona->getPresentation(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/excel/listFicher/{deposer}', name: 'list_ficher')]
    public function listFicher(Deposer $deposer, DeposerRepository $deposerRepository): Response
    {
        $fichers = $deposerRepository->findOneBy(['id' => $deposer->getId()]);

        $data = [];

        $data[] = [
            'id' => $deposer->getId(),
            'name' => $deposer->getNom(),
            'ext' => $deposer->getExt(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/excel/update', name: 'update_ficher', methods: ['POST'])]
    public function updateFicher(Request $request, PersonaRepository $personaRepository, EntityManagerInterface $entityManager): Response
    {
        $persona = $request->getPayload()->all()['persona'];
        $person = $personaRepository->findOneBy(['id' => $persona['id']]);


        ;
        if($person !== null) {
            $person->setNomDuGroupe($persona['nomeDuGroupe'])
                ->setOrigine($persona['origine'])
                ->setVille($persona['ville'])
                ->setAnneeDebut($persona['anne'])
                ->setAnneeSeparation($persona['separation'])
                ->setFodateurs($persona['fondateurs'])
                ->setMembers($persona['memberes'])
                ->setCourantMusical($persona['courant'])
                ->setPresentation($persona['presentation']);
            $entityManager->persist($person);
            $entityManager->flush();
        }

        $data[] = [
            'id' => $person->getId(),
            'deposer' => $person->getDeposer(),
            'nomeDuGroupe' => $person->getNomDuGroupe(),
            'origine' => $person->getOrigine(),
            'ville' => $person->getVille(),
            'anne' => $person->getAnneeDebut(),
            'separation' => $person->getAnneeSeparation(),
            'fondateurs' => $person->getFodateurs(),
            'memberes' => $person->getMembers(),
            'courant' => $person->getCourantMusical(),
            'presentation' => $person->getPresentation(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/excel/delete/{id}', name: 'app_delete_persona', methods: ['POST'])]
    public function delete(Request $request, Persona $persona, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($persona);
        $entityManager->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }
}


