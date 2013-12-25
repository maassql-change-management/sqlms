<?php
// Italian language 
// file by FRANCO TASSI - franco7tassi@gmail.com
// (http://www.linkedin.com/profile/view?id=73367896) 
// (http://www.facebook.com/franco.tassi) 
// Rev.0 - 25.3.2013 
// Read our wiki on how to translate: http://code.google.com/p/phpliteadmin/wiki/Localization
$lang = array(
	"direction" => "LTR",
	"date_format" => 'G:i \d\e\l j M, Y \A\.\D\. (T)',  // see http://php.net/manual/en/function.date.php for what the letters stand for
	"ver" => "versione",
	"for" => "per",
	"to" => "a",
	"go" => "Vai",
	"yes" => "Si",
	"sql" => "SQL",
	"csv" => "CSV",
	"csv_tbl" => "Table that CSV pertains to",
	"srch" => "Ricerca",
	"srch_again" => "Nuova ricerca",
	"login" => "Log In",
	"logout" => "Logout",
	"view" => "Vista",
	"confirm" => "Conferma",
	"cancel" => "Cancella",
	"save_as" => "Salva con nome",
	"options" => "Opzioni",
	"no_opt" => "Nessuna opzione",
	"help" => "Help",
	"installed" => "installato",
	"not_installed" => "non installato",
	"done" => "fatto",
	"insert" => "Inserisci",
	"export" => "Esporta",
	"import" => "Importa",
	"rename" => "Rinomina",
	"empty" => "Vuoto",
	"drop" => "Elimina",
	"tbl" => "Tabella",
	"chart" => "Chart",
	"err" => "ERRORE",
	"act" => "Azione",
	"rec" => "Records",
	"col" => "Colonna",
	"cols" => "Colonna(e)",
	"rows" => "riga(e)",
	"edit" => "Modifica",
	"del" => "Cancella",
	"add" => "Aggiungi",
	"backup" => "Backup database file",
	"before" => "Prima",
	"after" => "Dopo",
	"passwd" => "Password",
	"passwd_incorrect" => "Password errata.",
	"chk_ext" => "Controlla le estensioni SQLite PHP supportate",
	"autoincrement" => "Autoincremento",
	"not_null" => "Not NULL",
	"attention" => "Attenzione",
	"none" => "None",   #todo: translate
	"as_defined" => "As defined",  #todo: translate
	"expression" => "Expression",  #todo: translate
	
	"sqlite_ext" => "Estensione di SQLite",
	"sqlite_ext_support" => "Sembra che nessuna delle librerie SQLite supportate sono disponibili nella tua installazione di PHP. Potresti non essere in grado di usare %s finchè non ne installi almeno una.",
	"sqlite_v" => "Versione di SQLite",
	"sqlite_v_error" => "Sembra che il tuo database è un versione di SQLite %s ma la tua installazione PHP non contiene le estensioni necessarie per gestire questa versione. Per risolvere il problema, cancella il database and consenti a %s di crearlo automaticamente oppure ricrealo manualmente nella versione SQLite %s.",
	"report_issue" => "Il problema non può essere digniosticato The problem cannot be diagnosticato correttamente. Si prega di inviare un resoconto del problema a",
	"sqlite_limit" => "A causa delle limitazioni di SQLite, solo i campi nome e tipo di data possono essere modificati.",
	
	"php_v" => "Versione di PHP",
	
	"db_dump" => "database dump",
	"db_f" => "database file",
	"db_ch" => "Cambia Database",
	"db_event" => "Database Event",
	"db_name" => "Nome del database",
	"db_rename" => "Rinomina il Database",
	"db_renamed" => "Il Database '%s' è stato rinominato in",
	"db_del" => "Cancella il Database",
	"db_path" => "Percorso del database",
	"db_size" => "Dimensione del database",
	"db_mod" => "Ultima modifica del database",
	"db_create" => "Crea un nuovo database",
	"db_vac" => "Il database, '%s', è stato ridotto.",
	"db_not_writeable" => "Il database, '%s', non esiste e non può essere creato perchè la directory che lo ospita, '%s', non ha il permesso di scrittura. Il programma non è utilizzabile finchè non modifi i permessi di scrittura.",
	"db_setup" => "C'è stato un problema con il tuo database, %s. Verrà fatto un tentativo per scoprire cosa succede in modo da consentirti di sistemare il problema più facilemtne",
	"db_exists" => "Un database, un latro file oppure il nome di una directory '%s' già esiste.",
	
	"exported" => "Esportato",
	"struct" => "Struttura",
	"struct_for" => "struttura per",
	"on_tbl" => "Sulla tabella",
	"data_dump" => "Dump dei dati per",
	"backup_hint" => "Suggerimento: Per eseguire il backup del tuo database, la via più semplice è %s.",
	"backup_hint_linktext" => "scaricare il file del database",
	"total_rows" => "un totale di %s righe",
	"total" => "In Totale",
	"not_dir" => "La directory che hai specificato per eseguire la scansione del database non esiste oppure non è una directory.",
	"bad_php_directive" => "sembra che la direttiva PHP, 'register_globals' è abilitata. Questo è male. Hai bisogno di disabilitarla prima di continuare.",
	"page_gen" => "Pagina generata in %s secondi.",
	"powered" => "Powered by",
	"remember" => "Ricordami",
	"no_db" => "Benvenuto in %s. Sembra che tu abbia scelto di scansionare una directory per gestire i database. Comunque, %s potrebbe non trovare alcun valido database SQLite. Puoi usare la forma sottostante per creare il tuo primo database.",
	"no_db2" => "La directory che hai specificato non contiene alcun database da gestire, e la directory non è scrivibile. Questo significa che non puoi creare nessun nuovo database usando %s. Rendi la directory scrivibile oppure aggiungi manualmente del databases nella directory.",
	
	"create" => "Crea",
	"created" => "è stata creata",
	"create_tbl" => "Crea una nuova tabella",
	"create_tbl_db" => "Crea una nuova tabella sul database",
	"create_trigger" => "Crea un nuovo trigger sulla tabella",
	"create_index" => "Crea un nuovo indice sulla tabella",
	"create_index1" => "Crea Indice",
	"create_view" => "Crea una nuova view sul database",
	
	"trigger" => "Trigger",
	"triggers" => "Triggers",
	"trigger_name" => "Nome del Trigger",
	"trigger_act" => "Azione del Trigger",
	"trigger_step" => "Trigger Steps (semicolon terminated)",
	"when_exp" => "WHEN expression (type expression without 'WHEN')",
	"index" => "Indice",
	"indexes" => "Indici",
	"index_name" => "Nome dell'indice",
	"name" => "Nome",
	"unique" => "Unique",
	"seq_no" => "Seq. No.",
	"emptied" => "has been emptied",
	"dropped" => "è stato cancellato",
	"renamed" => "è stato(a) rinominato(a) in",
	"altered" => "è stata alterata con successo",
	"inserted" => "inserita",
	"deleted" => "cancellata",
	"affected" => "interessata",
	"blank_index" => "Il nome dell'indice non deve essere vuoto.",
	"one_index" => "Devi specificare una colonna indice.",
	"docu" => "Documentazione",
	"license" => "Licenza",
	"proj_site" => "Sito del progetto",
	"bug_report" => "Questo potrebbe essere un bug che necessita di essere riportato a",
	"return" => "Return",
	"browse" => "Browse",
	"fld" => "Campo",
	"fld_num" => "Numero di campi",
	"fields" => "Campi",
	"type" => "Tipo",
	"operator" => "Operatore",
	"val" => "Valore",
	"update" => "Update",
	"comments" => "Commmenti",
	
	"specify_fields" => "Devi specificare il numero di campi della tabella.",
	"specify_tbl" => "Devi specificare il nome della tabella.",
	"specify_col" => "Devi specificare una colonna.",
	
	"tbl_exists" => "Esiste già una tabella con lo stesso nome.",
	"show" => "Mostra",
	"show_rows" => "Mostra %s row(s). ",
	"showing" => "Showing",
	"showing_rows" => "Mostra righe",
	"query_time" => "(Query elaborata in %s sec)",
	"syntax_err" => "C'è un problema con la sintassi della tua query (La query non è stata eseguita)",
	"run_sql" => "Esegui la(e) query SQL sul database '%s'",
	
	"ques_empty" => "Sei sicuro di voler svuotare la tabella '%s'?",
	"ques_drop" => "Sei sicuro di voler eliminare la tabella '%s'?",
	"ques_drop_view" => "Sei sicuro di voler eliminare la view '%s'?",
	"ques_del_rows" => "Sei sicuro di voler cancellare la(e) riga(e) %s dalla tabella '%s'?",
	"ques_del_db" => "Sei sicuro di voler cancellare il database '%s'?",
	"ques_del_col" => "Sei sicuro di voler cancellare la(e) coonna(e) \"%s\" dalla tabella '%s'?",
	"ques_del_index" => "Sei sicuro di vole cancellare l'indice '%s'?",
	"ques_del_trigger" => "Sei sicuro di voler cancellare il trigger '%s'?",
	
	"export_struct" => "Export with structure",
	"export_data" => "Export with data",
	"add_drop" => "Add DROP TABLE",
	"add_transact" => "Add TRANSACTION",
	"fld_terminated" => "Fields terminated by",
	"fld_enclosed" => "Fields enclosed by",
	"fld_escaped" => "Fields escaped by",
	"fld_names" => "Nome del campo nella prima riga",
	"rep_null" => "Sostituisci NULL con",
	"rem_crlf" => "Rimuovi i caratteri CRLF all'interno dei campi",
	"put_fld" => "Metti i nomi dei campi nella prima riga",
	"null_represent" => "NULL represented by",
	"import_suc" => "Importato con successo.",
	"import_into" => "Importa dentro",
	"import_f" => "File da importare",
	"rename_tbl" => "Rinomina la tabella '%s' in",
	
	"rows_records" => "riga(e) partendo dal record # ",
	"rows_aff" => "riga(e) interessate. ",
	
	"as_a" => "as a",
	"readonly_tbl" => "'%s' è una view, questo significa che si tratta di un istruzione SELECT trattata come una tabella di sola lettura. Non puoi ne editarla ne inserire nuovi record.",
	"chk_all" => "Seleziona tutto",
	"unchk_all" => "Deseleziona tutto",
	"with_sel" => "Operazione selezionata",
	
	"no_tbl" => "Nessuna tabella nel database.",
	"no_chart" => "Se leggi questo, significa che il grafico potrebbe non essere generato. I dati che stai cercando di visualizzare potrebbero essere non appropriati per il grafico.",
	"no_rows" => "Non ci sono righe nella tabella per il range che hai selezionato.",
	"no_sel" => "Non hai selezionato nulla.",
	
	"chart_type" => "Tipo di grafico",
	"chart_bar" => "Grafico a barre",
	"chart_pie" => "Grafico a torta",
	"chart_line" => "Spezzata",
	"lbl" => "Etichette",
	"empty_tbl" => "Questa tabella è vuota.",
	"click" => "Clicca qui",
	"insert_rows" => "Per inserire righe.",
	"restart_insert" => "Ricomincia l'inserimento con ",
	"ignore" => "Ignora",
	"func" => "Funzione",
	"new_insert" => "Insierisci come Riga Nuova",
	"save_ch" => "Salva le Modifiche",
	"def_val" => "Valore di Default",
	"prim_key" => "Chiave Primaria",
	"tbl_end" => "campo(i) alla fine della tabella",
	"query_used_table" => "Query usata per creare questa tabella",
	"query_used_view" => "Query usata per creare questa view",
	"create_index2" => "Crea un indice su",
	"create_trigger2" => "Crea un nuovo trigger",
	"new_fld" => "Adding new field(s) to table '%s'",
	"add_flds" => "Aggiungi il campo",
	"edit_col" => "Editing column '%s'",
	"vac" => "Vacuum",
	"vac_desc" => "Large databases sometimes need to be VACUUMed per ridurre l'impronta sul server. Clicca il bottone sotto per eseguire il VACUUM del database '%s'.",
	"event" => "Event",
	"each_row" => "Per ogni riga",
	"define_index" => "Define index properties",
	"dup_val" => "Duplica i valori",
	"allow" => "Consentito",
	"not_allow" => "Con consentito",
	"asc" => "Ascendente",
	"desc" => "Discendente",
	"warn0" => "Sei stato avvisato.",
	"warn_passwd" => "Stai usando la password di default, può essere pericoloso. Puoi cambiarla facilmente editando %s.",
	"warn_dumbass" => "Non hai cambiato dumbass ;-)",
	#todo: translate
	"counting_skipped" => "Counting of records has been skipped for some tables because your database is comparably big and some tables don't have primary keys assigned to them so counting might be slow. Add a primary key to these tables or %sforce counting%s.",
	"sel_state" => "Seleziona l'istruzione",
	"delimit" => "Delimitatore",
	"back_top" => "Torna in cima",
	"choose_f" => "Scegli il File",
	"instead" => "Invece di",
	"define_in_col" => "Definisci index column(s)",
	
	"delete_only_managed" => "Puoi cancellare solamente i database gestiti da questo strumento!",
	"rename_only_managed" => "Puoi rinominare solamente i database gestiti da questo strumento!",
	"db_moved_outside" => "Hai certato di spotare dentro una direcotry dove non può essere gestita anylonger, oppure il controllo è fallito per la mancanza di diritti.",
	"extension_not_allowed" => "L'estensione che hai fornito non è contenuta nella lista delle estensioni consentire. Per favore usa una delle seguenti estensioni",
	"add_allowed_extension" => "Puoi aggiungere estensioni per questa lista aggiungendo la tua estensione to \$allowed_extensions nella configurazione.",
	"directory_not_writable" => "Il file del database è di per se editabile, ma per poterci scrivere, anche la direcotory che lo ospita deve essere aggiornabile. Questo perchè SQLite ha bisogno di inserirvi file temporanei per il locking.",
	"tbl_inexistent" => "La tabella %s non esiste",

	// errors that can happen when ALTER TABLE fails. You don't necessarily have to translate these.
	"alter_failed" => "Altering of Table %s failed",
	"alter_tbl_name_not_replacable" => "could not replace the table name with the temporary one",
	"alter_no_def" => "nessuna definzione ALTER",
	"alter_parse_failed" =>"fallito il parsing (controllo) della definzione ALTER",
	"alter_action_not_recognized" => "l'azione ALTER non è stata riconosciuta",
	"alter_no_add_col" => "non è stata rilevata nessuna colonna da aggiungere nell'istruzione ALTER",
	"alter_pattern_mismatch"=>"La sequenza non ha combaciato sulla tua istruzione originale CREATE TABLE",
	"alter_col_not_recognized" => "non è stata rilevato il nome della nuova o della vecchia colonna",
	"alter_unknown_operation" => "L'operazione ALTER non è riconosciuta!",
	
	/* Help documentation */
	"help_doc" => "Documentazione",
	"help1" => "SQLite Librerie di Estensioni",
	"help1_x" => "%s usa Librerie di Estensioni di PHP che consentono di interagire con i database SQLite. Attualmente, %s supporta PDO, SQLite3, e SQLiteDatabase. Sia PDO che SQLite3 trattano la versione 3 di SQLite, mentre SQLiteDatabase tratta con la versione 2. Così, se la tua installazione PHP include più di una libreria di estesione SQLite, PDO e SQLite3 avranno la precedenza nel fare uso della tecnologia migliore. Comunque, se possiedi database che sono nella versione 2 di SQLite, %s forzerà ad usare SQLiteDatabase solamente per quei database. Non tutti i database hanno bisogno di essere della stessa versione. Durante la creazione del database, comunque, l'estenzione verrà utilizzata l'estenzione più avanzata.",
	"help2" => "Creare un Nuovo Database",
	"help2_x" => "Quando crei un nuovo database, il nome che inserisci sarà appeso con l'estensione del file appropriata (.db, .db3, .sqlite, etc.) se non la includi tu stesso. Il database verrà creato nella directory che tu specifichi come directory \$directory variable.",
	"help3" => "Tabelle vs. Viste",
	"help3_x" => "Sulla pagina del database principale, c'è una lista di tabele e viste. Poichè le view sono di sola lettura, certe operazioni verranno disabilitate. Al posto di queste operazioni disabilitate verranno mostrati spazi vuoti (omissioni) nella righa di comando della vista. Se vuoi cambiare il dato di una vista, devi cancellare la vista e crearne una nuova con l'istruzione SELECT desiderata che interroga altre tabelle esistenti. Per maggiori informazioni, guarda <a href='http://en.wikipedia.org/wiki/View_(database)' target='_blank'>http://en.wikipedia.org/wiki/View_(database)</a>",
	"help4" => "Scrivere un'istruzione di selezione per una nuova View",
	"help4_x" => "Quando crei una nuova view, devi scrivere un istruzione SQL SELECT che verrà usata come suo dato. Una view è semplicemente una tabella di sola lettura alla quale si può accedere e porre interrogazioni come una normale tabella , ad eccezione del fatto che non può essere modificata con inserimenti, editing di colonna, or editing di riga. E' usata soltamente per estrapolare dati.",
	"help5" => "Exportazione della Struttura verso il file SQL",
	"help5_x" => "Durante il processo di esportazione verso un file SQL, puoi scegliere di includere le istruzioni (query) che consentono di creare tabella e colonne.",
	"help6" => "Esporta i dati verso il File SQL",
	"help6_x" => "Durante il processo di esportazione verso un file SQL, puoi scegliere di includere le istruzioni (query) che popolano la tabella(e) with the current records of the table(s).",
	"help7" => "Aggiungi Drop Table (cancella tabella) al File SQL esportato",
	"help7_x" => "Durante il processo di esportazione verso un file SQL, puoi scegliere di includere le istruzioni (query) per cancellare (DROP) le tabelle esistenti prima di aggiungerle così che non occorreranno problemi cercando di crearetabelle che già esistono.",
	"help8" => "Aggiungi Transaction al File SQLto esportato",
	"help8_x" => "Durante il processo di esportazione verso un file SQL, puoi scegliere di includere le istruzioni (query) around a TRANSACTION so that if an error occurs at any time during the importation process using the exported file, the database can be reverted to its previous state, preventing partially updated data from populating the database.",
	"help9" => "Aggiungi commenti al File esportato",
	"help9_x" => "Durante il processo di esportazione verso un file SQL, puoi includere commenti spiegano ogni passo del processo così che umano può comprendere meglio cosa sta succedendo."
	
	);


?>
