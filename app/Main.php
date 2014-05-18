<?php
class Main extends GtkWindow
{
    const APP_NOME  = 'LePeso';

    private $campos = array();
    private $serial;
    private $porta;

    /**
     * Construtor
     *
     * @param  string $porta Porta para efetuar a leitura
     * @return  void
     */
    public function __construct($porta = 'COM3')
    {
        parent::__construct();

        // detalhes
        $this->set_title(self::APP_NOME);
        $this->set_default_size(0, 0);
        $this->set_position(Gtk::WIN_POS_CENTER);

        // tratando o fechamento da janela
        $this->connect_simple('destroy', array('gtk', 'main_quit'));

        // container
        $main = new GtkVBox;
        $main->pack_start($this->insereCampo('Peso em Kg', 60), false);
        $main->pack_start($this->insereCampo('Preço por Kg', 60), false);
        $main->pack_start($this->insereCampo('Preço Total', 60), false);

        $botaoAbrirPorta = new GtkButton;
        $botaoAbrirPorta->set_label('Abrir Porta');
        $botaoAbrirPorta->connect_simple('clicked', array($this, 'abrePorta'));

        $botaoFecharPorta = new GtkButton;
        $botaoFecharPorta->set_label('Fechar Porta');
        $botaoFecharPorta->connect_simple('clicked', array($this, 'fechaPorta'));

        $botaoLerPeso = new GtkButton;
        $botaoLerPeso->set_label('Ler Peso');
        $botaoLerPeso->connect_simple('clicked', array($this, 'lePeso'));

        $botaoFechar = new GtkButton;
        $botaoFechar->set_label('Sair');
        $botaoFechar->connect_simple('clicked', array('gtk', 'main_quit'));

        $botoes = new GtkHButtonBox;
        $botoes->add($botaoAbrirPorta);
        $botoes->add($botaoFecharPorta);
        $botoes->add($botaoLerPeso);
        $botoes->add($botaoFechar);

        // insere os botões no container
        $main->add($botoes);

        // insere o container na tela principal
        $this->add($main);

        // setando a porta
        $this->porta = trim($porta);

        $this->show_all();
    }

    /**
     * Abre a porta para efetuar a leitura
     *
     * @return  boolean
     */
    public function abrePorta()
    {
        // abre a porta serial
        $this->serial = new PhpSerial;
        $this->serial->deviceSet($this->porta);
        $this->serial->confBaudRate(9600);
        $this->serial->confParity("none");
        $this->serial->confCharacterLength(8);
        $this->serial->confStopBits(2);
        $this->serial->confFlowControl("none");

        // se n conseguir abrir a porta, aborto tudo
        if (false == $this->serial->deviceOpen('r+b')) {
            $dialog = new GtkMessageDialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, 'Erro abrindo porta');
            $dialog->run();
            $dialog->destroy();

            return false;
        }

        return true;
    }

    /**
     * Fecha a porta para liberar o recurso
     *
     * @return  boolean
     */
    public function fechaPorta()
    {
        // se n conseguir abrir a porta, aborto tudo
        if (null == $this->serial) {
            $dialog = new GtkMessageDialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, 'Erro fechando porta');
            $dialog->run();
            $dialog->destroy();

            return false;
        }

        $this->serial->deviceClose();
        $this->serial = null;

        return true;
    }

    /**
     * Le o peso da balança, fica num loop infinito até retornar algo da porta
     *
     * @return boolean
     */
    public function lePeso()
    {
        // se n conseguir abrir a porta, aborto tudo
        if ($this->serial == null or $this->serial == false) {
            $dialog = new GtkMessageDialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, 'A porta não está aberta');
            $dialog->run();
            $dialog->destroy();

            return false;
        }

        $read = null;

        do {
            $write = $this->serial->sendMessage(chr(4) . chr(5));

            sleep(1);

            $read  = $this->serial->readPort();

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

            $this->campos['Peso em Kg']->set_text($peso);
            $this->campos['Preço por Kg']->set_text($precoKg);
            $this->campos['Preço Total']->set_text($precoTotal);

            return true;
        } while ($read === null);
    }

    /**
     * Cria linha com campos
     *
     * @param string $label
     * @param integer $largura
     * @return GtkHBox
     */
    private function insereCampo($label, $largura = 60, $altura = 20)
    {
        $labelCampo = new GtkLabel;
        $labelCampo->set_text(utf8_decode($label));
        $labelCampo->set_size_request(100, $altura);

        $inputCampo = new GtkEntry;
        $inputCampo->set_size_request($largura, $altura);

        $box = new GtkHBox;
        $box->pack_start($labelCampo, false);
        $box->pack_start($inputCampo, true);

        $this->campos[ $label ] = $inputCampo;

        return $box;
    }
}