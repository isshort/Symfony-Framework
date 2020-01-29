<?php
//https://ourcodeworld.com/articles/read/245/how-to-execute-plain-sql-using-doctrine-in-symfony-3
// php bin/console doctrine:query:sql 'ALTER TABLE student CHANGE `email` `emailAddress` varchar(50)'
namespace App\Controller;

use App\Entity\Drink;
use App\Form\SignUpType;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class StudentController extends AbstractController
{
    private $stateArray;
    private $searchArray=array();
    function  setSearchArray($array){
        $this->searchArray=$array;
    }
    function getSearchArray(){
        return $this->searchArray;
    }
    function setStateArray($index){
        $this->stateArray=$index;
    }
    function getStateArray(){
        return $this->stateArray;
    }

    /**
     * @Route("/", name="Main")
     */
    public function Main(Request $request)
    {
        return $this->render('index.html.twig', [

        ]);

    }

    /**
     * @Route("/showallstudent", name="showallstudent")
     */
    public function index()
    {

        $repository = $this->getDoctrine()->getRepository(Drink::class);

        $students = $repository->findAll();
        //$em = $this->getDoctrine()->getEntityManager();
       // $students=$repository->createQuery("Select * from Worker\Entity\student");

        return $this->render('student/show_all_students.html.twig', ["students" => $students]);
    }

    /**
     * @Route("/student/search", name="searchsomestudentform")
     */
    public function searchCustomer(StudentRepository $repository,Request $request)
    {

        $repository = $this->getDoctrine()->getRepository(Drink::class);
//        $students=$repository->findOneBy(
//            [
//            'name'=>"Namatullah",
//            ]
//
//        );

       //
//        $students = $this->getDoctrine()
//            ->getRepository(Drink::class)
//            ->find(1);
//        $students = $repository->findBy(
//            ['name' => 'Namatullah',
//            'last_name'=>'Wahidi']);
//         $students = $repository->findBy(
//            ['name' => 'Namatullah']
//        );
     //   var_dump($students);

//        $students = $this->getDoctrine()
//            ->getRepository(Drink::class)
//            ->findAllGreater("Namatullah");


        $em = $this->getDoctrine()->getManager();

       // $RAW_QUERY = 'SELECT * FROM student where last_name=:name1';// and name=:name2';
        $name=$request->request->get('name');
        $price=$request->request->get('price');
        $company=$request->request->get('company');
        $amount=$request->request->get('amount');
        $RAW_QUERY=$this->GenerateSQL($name,$price,$company,$amount);
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        foreach ($this->getSearchArray() as $key=>$value){
           // echo "key is:".$key."  and values is ".$value;
            $statement->bindValue($key,$value);
        }

        //$statement->bindValue('name1', "wahidi");
       // $statement->bindValue('name2',"Namatullah");

        $statement->execute();

        $students = $statement->fetchAll();
       // var_export($students);

        $items = array();
        foreach ($students as $doc){
            $items[] = $doc;
        }
        if (!$students) {
            throw $this->createNotFoundException(
                'No customer found for id '
            );
        }
        return $this->render('student/show_all_students.html.twig', ["students" => $students]);
    }
    public function GenerateSQL($name,$price,$company,$amount){
        $sql = "Select * From drink ";
        $arr=array();
        if (($name=="") && ($price==0)  && ($company=="") &&($amount==0)  ) {
            $sql = "select  * from drink";
            $this->setStateArray(0);
        }else{
            $this->setStateArray(1);
            $sql=$sql." where ";
            $tail="";
            if($name!=""){
                $sql=$sql." name=:name ";
                $arr['name']=$name;
                $tail="and";
            }
            if($price!=0){
                if($tail!=""){
                    $sql=$sql." and price=:price";
                }else{
                    $sql=$sql." price=:price";
                    $tail="and";
                }
                $arr['price']=$price;
            }
            if($company!=""){
                if($tail!=""){
                    $sql=$sql." and company=:company";
                }else{
                    $sql=$sql." company=:company";
                    $tail="and";
                }
                $arr['company']=$company;
            }
            if($amount!=0){
                if($tail!=""){
                    $sql=$sql." and amount=:amount";
                }else{
                    $sql=$sql." amount=:amount";
                    $tail="and";
                }
                $arr['amount']=$amount;
            }

        }
        $this->setSearchArray($arr);
        return $sql;

    }
    /**
     * @Route("/student/searchsomestudent", name="searchsomestudent")
     */
    public function searchSomWorker(){
        return $this->render('student/searchsomestudent.html.twig',[]);
    }



    /**
     * @Route("/student/updatestudentform/{id}", name="updatestudentform",methods={"GET"})
     * @param Drink $student
     * @return Response
     */
    public function UpdateStudentform(Drink $student):Response
    {
        return $this->render('student/update.html.twig', [
            'student'=>$student,
        ]);
    }

    /**
     * @Route("/student/updatestudent/{id}", name="updatestudent")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function UpdateStudent(Request $request,$id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->getRepository(Drink::class)->find($id);
        if (!$student) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $student->setName($request->request->get('name'));
        $student->setPrice($request->request->get('price'));
        $student->setCompany($request->request->get('company'));
        $student->setAmount($request->request->get('amount'));
        $entityManager->flush();

        $repository = $this->getDoctrine()->getRepository(Drink::class);
        $all = $repository->findAll();

        return $this->render('student/show_all_students.html.twig', [
            'students' => $all,
        ]);
    }
    /**
     * @Route("/student/newstudent", name="newstudentform")
     */
    public function newStudentForm()
    {
        return $this->render('student/newstudentform.html.twig', [

        ]);
    }

    /**
     * @Route("/student/addnewstudent", name="addNewstudent")
     * @param Request $request
     * @return Response
     */
    public function addNewStudentForm(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $student = new Drink();
        $student->setName($request->request->get('name'));
        $student->setPrice($request->request->get('price'));
        $student->setCompany($request->request->get('company'));
        $student->setAmount($request->request->get('amount'));
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($student);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        $repository = $this->getDoctrine()->getRepository(Drink::class);
        $all = $repository->findAll();

        return $this->render('student/show_all_students.html.twig', [
            'students' => $all,
        ]);
    }
    /**
     * @Route("/student/deletestudent/{id}", name="deletestudent")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function DeleteStudent(Request $request,$id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $student = $entityManager->getRepository(Drink::class)->find($id);
        if (!$student) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($student);
        $entityManager->flush();
        $repository = $this->getDoctrine()->getRepository(Drink::class);
        $all = $repository->findAll();

        return $this->render('student/show_all_students.html.twig', [
            'students' => $all,
        ]);
    }
}
