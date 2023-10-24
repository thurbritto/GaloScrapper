<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

class Scrapper {
    
    public function scrap(\DOMDocument $dom): array {
        $papers = [];
        $xpath = new \DOMXPath($dom);
        

        // XPath dos elementos para scrap
        $idXPath = "//div[@class='volume-info']";
        $titleXPath = "//h4[@class='my-xs paper-title']";
        $typeXPath = "//div[@class='tags mr-sm']";
        $authorsXPath = "//div[@class='authors']/span";


        // Query dos elementos
        $idElements = $xpath->query($idXPath);
        $titleElements = $xpath->query($titleXPath);
        $typeElements = $xpath->query($typeXPath);
        $authorElements = $xpath->query($authorsXPath);

        // Loop para criar os respectivos papers
        foreach ($titleElements as $i => $titleElement) {
          $id = $idElements->item($i)->textContent;
          $title = $titleElement->textContent;
          $type = $typeElements->item($i)->textContent;
      
          // Definindo o contexto do XPath para o pai do elemento atual (para evitar coletar autores de outros proceedings)
          $titleContext = $titleElement->parentNode;
      
          // Query após redefinir o contexto do XPath 
          $authorElements = $xpath->query('div[@class="authors"]/span', $titleContext);
      
          // Array para armazenar os autores
          $authors = [];
      
          // Instanciando objetos Person e adicinando à array
          foreach ($authorElements as $authorElement) {
              $name = $authorElement->textContent;
              $institution = $authorElement->getAttribute('title');
              $authors[] = new Person($name, $institution);
          }
      
          // Instanciando objetos Paper com os dados coletados
          $papers[] = new Paper($id, $title, $type, $authors);
      }

        return $papers;
    }
}
