<?php


namespace Chuva\Php\WebScrapping;

require 'vendor/autoload.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;


/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new \DOMDocument('1.0', 'utf-8');
    @$dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $data = (new Scrapper())->scrap($dom);

    // Criando um objeto de escrita de planilha
    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToFile('output.xlsx');

    // Array para armazenar os cabeçalhos base
    $header = [
      'ID',
      'Title',
      'Type',
    ];

    // Cabeçalho dinâmico para quantidade máxima de autores
    for ($i = 1; $i <= max(array_map(function ($paper) {
      return count($paper->authors);
    }, $data)); $i++) {
      $header[] = "Author $i";
      $header[] = "Author $i Institute";
    }

    $writer->openToFile('output.xlsx');

    // Inserindo cabeçalho base na planilha
    $writer->addRow(WriterEntityFactory::createRowFromArray($header));

    foreach ($data as $paper) {
      // Array para dados do paper
      $rowData = [
          $paper->id,
          $paper->title,
          $paper->type,
      ];

      // Obtendo informações dos autores e instituições
      foreach ($paper->authors as $author) {
          $rowData[] = rtrim($author->name, ';');
          $rowData[] = $author->institution;
      }

      // Inserindo colunas vazias, se houver menos autores do que o máximo
      $missingAuthors = count($header) - count($rowData);
      for ($i = 0; $i < $missingAuthors; $i++) {
          $rowData[] = ''; // Preenche com células vazias, se necessário
      }

      // Inserindo dados na planilha
      $writer->addRow(WriterEntityFactory::createRowFromArray($rowData));
    }
    // Fecha o escritor de planilha
    $writer->close();
    
    echo "Planilha salva com sucesso em output.xlsx\n";
  }
}
