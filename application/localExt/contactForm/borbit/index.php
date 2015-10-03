<?php
/*
 ��������� Contact Form (���������� �����)
*/

include_once(ENGINE_PATH.'interface/interfaceLocalExt.php');

class ContactForm implements LocalExtInterface {

    #PRIVATE VARIABLES
    private $settings;
    private $fields;
    private $form;

    #PUBLIC VARIABLES

    /*
     �����������
    */
    public function __construct() {
        $this->parseSettings();
        $this->renderForm();
    }

    /*
     ����������
    */
    public function __destruct() {

        $this->settings = NULL;

    }

    /*
     ��������� ����������, ������� �������� �� ini-�����
    */
    public function parseSettings() {
        $this->settings = parse_ini_file('settings.ini', true);
        $this->fields   = parse_ini_file('contacts_fields.ini', true);

        /*echo '<pre>';
        var_dump($this->settings);
        echo '</pre>';*/
    }

    /*
     ���������� ��������� ������ ����������
    */
    public function getResult() {
        return $this->form;
    }

    /*
     ����������� HTML-��� ����� ���������
    */
    private function renderForm() {

        $this->form .= '<form action="'.$this->settings['FORM']['action'].'" method="'.$this->settings['FORM']['method'].'">
                        <table border="0" cellspacing="0" cellpadding="5">';

        foreach($this->fields AS $field) {

            $this->form .= '<tr>
                            <td>'.$field['title'].'</td>';

            if($field['type'] == 'text') {
                $this->form .= '<td><input type="text" name="'.$field['name'].'" /></td>';
            }

            if($field['type'] == 'textarea') {
            }

            if($field['type'] == 'select') {
            }

            if($field['type'] == 'button') {
            }

            if($field['type'] == 'checkbox') {
            }

            if($field['type'] == 'radio') {
            }

            $this->form .= '</tr>';

        }

        $this->form .= '<tr><td></td><td><input type="submit"/></td></tr>
                        </table>
                        </form>';

    }

    /*
     �������� ����
    */
    private function sendEmail() {

    }
}
?>