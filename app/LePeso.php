<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
use serial\serial as Dio;
use PhpSerial as Serial;
use GtkMessageDialog as Dialog;

class LePeso
{
    /**
     * @var PhpSerial
     */
    protected $serial;

    /**
     * @var serial\serial
     */
    protected $dio;

    /**
     * Abre a porta para efetuar a leitura
     *
     * @return  resource
     */
    public function __construct($porta = '/dev/ttyUSB0')
    {
        $this->dio = new Dio($porta, O_RDWR);
        $this->dio->set_options(array(
            "baud"   => 9600,
            "bits"   => 8,
            "stop"   => 2,
            "parity" => 0,
        ));
        $this->dio->write(chr(04) . chr(05));
        $this->dio->close();

        $this->serial = new Serial;
        $this->serial->deviceSet($porta);
        $this->serial->confBaudRate(9600);
        $this->serial->confParity("none");
        $this->serial->confCharacterLength(8);
        $this->serial->confStopBits(2);
        $this->serial->confFlowControl("none");
        $this->serial->deviceOpen('r+b');

        // se n conseguir abrir a porta, aborto tudo
        if (false == $this->serial) {
            $dialog = new Dialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, 'Erro abrindo porta.');
            $dialog->run();
            $dialog->destroy();

            return false;
        }

        return $this->serial;
    }

    /**
     * Le o peso da balança, fica num loop infinito até retornar algo da porta
     *
     * @return string
     */
    public function read()
    {
        // se n conseguir abrir a porta, aborto tudo
        if ($this->serial == null or $this->serial == false) {
            $dialog = new Dialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, utf8_decode('A porta não está aberta.'));
            $dialog->run();
            $dialog->destroy();

            return false;
        }

        $read       = null;
        $tentativas = 1;
        $limite     = 10;

        while (strlen($read) !== 56 && $tentativas < $limite) {
            $this->serial->sendMessage(chr(04) . chr(05) . ' ');
            $read = $this->serial->readPort();

            $tentativas++;
        }

        if (strlen($read) !== 56) {
            $dialog = new Dialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, utf8_decode('Não foi possível efetuar a leitura.'));
            $dialog->run();
            $dialog->destroy();

            return false;
        }

        return $read;
    }

    /**
     * Lê o retorno da balança e retorna os valores corretamente
     *
     * @param  string $read
     * @return array
     */
    public function decode($read)
    {
        /*
         * Os dados da impressora vem separados pelo caracter de ESCAPE (chr 27 ou 0x1B)
         * Deve vir um total de 56 caracteres separados da seguinte forma
         *
         * 18 a 23 - peso
         * 30 a 36 - informação do preço por kg
         * 43 a 49 - informação do total a pagar
         */
        preg_match_all('/[0-9]{1,3},[0-9]{2,3}/', substr($read, 18, 23), $pesoValor);
        $peso       = (array_key_exists(0, $pesoValor[0])  ? $pesoValor[0][0] : 0);
        $precoKg    = (array_key_exists(1, $pesoValor[0])  ? $pesoValor[0][1] : 0);

        preg_match_all('/[0-9]{1,3},[0-9]{2}/', substr($read, 43, 49), $precoValor);
        $precoTotal = (array_key_exists(0, $precoValor[0]) ? $precoValor[0][0] : 0);

        return array($peso, $precoKg, $precoTotal);
    }
}