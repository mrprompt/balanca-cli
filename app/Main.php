<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
class Main extends GtkWindow
{
    const APP_NOME  = 'LePeso';
    private $campos = array();
    private $driver;

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

        $botao = new GtkButton;
        $botao->set_label('Ler Peso');
        $botao->connect_simple('clicked', array($this, 'lePeso'));

        // insere os botões no container
        $main->add($botao);

        // insere o container na tela principal
        $this->add($main);

        // setando a porta
        $this->driver = new LePeso($porta);

        // boot
        $this->show_all();

        Gtk::main();
    }

    /**
     * Le o peso da balança, fica num loop infinito até retornar algo da porta
     *
     * @return boolean
     */
    public function lePeso()
    {
        $read   = $this->driver->read();
        $pesos  = $this->driver->decode($read);

        $this->campos['Peso em Kg']->set_text($pesos[0]);
        $this->campos['Preço por Kg']->set_text($pesos[1]);
        $this->campos['Preço Total']->set_text($pesos[2]);

        return true;
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