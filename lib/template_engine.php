<?php
/**
 * La classe viene usata per gestire dei templates di codice html che contengono dei tag che poi saranno sostituiti
 */
class Template
{
    // definizione del pattern per i tag
    private const PATT_BEGIN = '<component>';
    private const PATT_END = '</component>';

    private string $template_name;
    private string $data;

    /**
     * @brief Costruttore
     * @param string $template_name nome del template
     * @param string $data contenuto del template
     */
    public function __construct(string $template_name, string $data)
    {
        $this->template_name = $template_name;
        $this->data = $data;
    }

    /**
     * Verifica se il template è vuoto
     * @return bool true se il template è vuoto, false altrimenti
     */
    public function is_empty(): bool
    {
        return empty($this->data);
    }

    /**
     * Sostituisce un tag con un valore, il tag deve essere nella forma <component>tag</component> e il valore può essere una stringa
     * @param string $tag nome del tag
     * @param string $value valore da sostituire al tag
     * @throws Exception se il tag non è stato sostituito o se piu' di un tag è stato sostituito
     * @return void
     */
    public function insert(string $tag, string $value): void
    {
        // definizione del pattern
        $pattern = self::PATT_BEGIN . $tag . self::PATT_END;

        $changes = 0;

        // sostituzione del tag con il valore
        $this->data = str_replace($pattern, $value, $this->data, $changes);

        // controllo che il tag sia stato sostituito
        if ($changes != 1) {
            throw new Exception("Error while replacing the tag $tag
                                in the template $this->template_name:
                                $changes replacements were made instead
                                 of 1");
        }
    }

    /**
     * Sostituisce un tag con un valore come insert() ma controlla che il tag contenga con "action"
     * @param string $tag nome del tag
     * @param string $value valore da sostituire al tag
     * @throws Exception se il tag non inizia con "action"
     * @return void
     */
    public function insert_action(string $tag, string $value): void
    {
        // definizione del pattern
        $pattern = self::PATT_BEGIN . $tag . self::PATT_END;

        // controllo che il tag contenga "action"
        $check = substr($tag, 0, 6);
        if ($check != "action") {
            throw new Exception("Error: the tag $tag do not begin with action");
        }


        $changes = 0;

        // sostituzione del tag con il valore
        $this->data = str_replace($pattern, $value, $this->data, $changes);
    }

    /**
     * Controlla che tutti i tag siano stati sostituiti e restituisce il template
     * @throws Exception se ci sono tag non sostituiti
     * @return string il template
     */
    public function build(): string
    {
        // definizione del pattern
        $pattern_begin = self::PATT_BEGIN;
        $pattern_end = self::PATT_END;

        // ricerca dei tag non sostituiti
        $pattern = "~{$pattern_begin}([a-zA-Z0-9_]+){$pattern_end}~";
        $matches = array();
        preg_match_all($pattern, $this->data, $matches);

        // se ci sono tag non sostituiti viene lanciata un'eccezione
        if (!empty($matches[1])) {
            throw new Exception("Error while building the template $this->template_name:
                                the following tags were not replaced:
                                " . implode(", ", $matches[1]));
        }

        // restituzione del template
        return $this->data;
    }

}

/**
 * La classe viene usata per caricare i templates
 */
class TemplateEngine
{
    // definizione della cartella dei templates
    private const TEMPLATES_DIR = "templates";

    /**
     * Controlla che la cartella dei templates esista
     * @throws Exception se la cartella non esiste
     * @return void
     */
    public function __construct()
    {
        if (!file_exists(self::TEMPLATES_DIR)) {
            throw new Exception("The templates directory does not exist");
        }
    }

    /**
     * Distruttore
     */
    public function __destruct()
    {
    }

    /**
     * Carica un template
     * @param string $template_name nome del template
     * @throws Exception se il template non esiste, se non è stato letto o se è vuoto
     * @return Template il template caricato
     */
    public function load_template(string $template_name): Template
    {
        // definizione del path del template
        $template_path = self::TEMPLATES_DIR . "/" . $template_name;

        // controllo che il template esista
        if (!file_exists($template_path)) {
            throw new Exception("The template $template_name does not exist");
        }
        $template_data = file_get_contents($template_path);

        // controllo che il template sia stato letto
        if ($template_data === false) {
            throw new Exception("Could not read the template $template_name");
        }

        // creazione del template
        $template = new Template($template_name, $template_data);

        // controllo che il template non sia vuoto
        if ($template->is_empty()) {
            throw new Exception("The template $template_name is empty");
        }

        // restituzione del template
        return $template;
    }
}
