<?php
/**
 * Created by PhpStorm.
 * User: Desenvolvimento
 * Date: 14/03/2018
 * Time: 16:35
 */

class ExportPDF extends Clients_controller
{
    public function __construct()
    {
        parent::__construct();

    }
    public function exportpdf_knowledge_base($id)
    {
        $this->load->model('knowledge_base_model');
        $data['article'] = $this->knowledge_base_model->get($id);
//        print_r($id);
        $this->load->library('Pdf');
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle($data['article']->subject);
        $pdf->SetHeaderMargin(30);
        $pdf->SetTopMargin(10);
        $pdf->setFooterMargin(20);
        $pdf->SetAutoPageBreak(true);
        if(PAINEL == INORTE)
            $pdf->SetAuthor('Inorte Sistemas');
        else
            $pdf->SetAuthor('Sistemas Quantum');
        $pdf->SetDisplayMode('real', 'default');

        $pdf->AddPage();

//                $pdf->Image('https://sistemaquantumlocal.ddns.net/crm/uploads/company/favicon.png', 75, 70, 60, 60, '', '', '', true, 500);
        if(PAINEL == INORTE){
            $pdf->writeHTMLCell('', '', '', '', html_entity_decode("<h2 align='center>'>" . $data['article']->subject . "</h2><hr><p></p>" . $data['article']->description));
        }else {
            if ($data['article']->staff_article && is_staff_member())
                $pdf->writeHTMLCell('', '', '', '', html_entity_decode("<h2 align='center>'>" . $data['article']->subject . "</h2><hr><p></p>" . $data['article']->description));
            else
                $pdf->writeHTMLCell('', '', '', '', html_entity_decode("<h1>Não autorizado</h1>"));
        }
        $pdf->Output('My-File-Name.pdf', 'I');

    }

}