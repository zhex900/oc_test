<?php
/**
 * Created by PhpStorm.
 * User: jake
 * Date: 22/1/19
 * Time: 1:04 PM
 */

namespace App\Services;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;

class ArticleScraper
{
    private $base_url;

    public function __construct()
    {
        $this->base_url = '';
    }

    public function articleSearch($search, $page=0)
    {
        $client = new Client();

        $paging='';

        if($page>0){
            $paging ='/page/'.$page;
        }

        try {

            $response = $client->get($this->base_url . $paging . '/?s=' . $search);
            $dom = new Dom;

            $dom->load($response->getBody());
            $articles = $dom->find('article');

            return ['pagination' => $this->getPagination($dom), 'articles' => $this->parseArticles($articles)];

        }catch (\Exception $exception){

            return $exception->getMessage();
        }


    }

    public function articleScraper()
    {
        $client = new Client();
        $response = $client->get($this->base_url);

        $dom = new Dom;

        $dom->load($response->getBody());

        $articles = $dom->find('.slides');

        $result['hot'] = $this->parseArticles($articles[0]->find('li'));
        $result['recent'] = $this->parseArticles($articles[1]->find('li'));

        return $result;
    }

    private function parseArticles($articles)
    {
        $json_articles = [];

        foreach ($articles as $articleHtml) {

            $json_articles[] = $this->parseArticle($articleHtml);
        }

        return $json_articles;
    }


    private function parseArticle(HtmlNode $article_html)
    {
        $article['category'] = $this->parseCategory($article_html);
        $article['href'] = $article_html->find('a')->getAttribute('href');
        $article['author']['name'] = html_entity_decode($article_html->find('.cb-author')->find('a')->innerHtml);
        $article['author']['href'] = $article_html->find('.cb-author')->find('a')->getAttribute('href');
        $article['img'] = $article_html->find('img')->getAttribute('src');
        $article['title'] = html_entity_decode($article_html->find('.cb-post-title')->find('a')->innerHtml);
        $article['excerpt'] = html_entity_decode($this->parseExcerpt( $article_html ) );
        $article['tags'] = $this->parseTags($article_html);
        return $article;
    }

    private function parseTags(HtmlNode $article){
        preg_match('/tag-.*/', $article->getAttribute('class'), $tags);

        if( sizeof($tags)>0){
            $tags = explode(' ',$tags[0]);
        }

        return $tags;
    }

    private function parseExcerpt(HtmlNode $article){
        try {
            return strip_tags($article->find('.cb-excerpt')->innerHtml);
        }catch (\Exception $exception){
            // excerpt does not exist
            return '';
        }
    }

    private function parseCategory(HtmlNode $article)
    {
        $categories = $article->find('.cb-category');
        $json_categories = [];
        if (sizeof($categories) > 0) {
            foreach ($categories->find('a') as $category_html) {
                $category['title'] = html_entity_decode($category_html->getAttribute('title'));
                $category['href'] = $category_html->getAttribute('href');
                $category['categoryName'] = html_entity_decode($category_html->innerHtml);
                $json_categories[] = $category;
            }
        }
        return $json_categories;
    }

    private function getPagination(Dom $dom)
    {
        $page_numbers = $dom->find('.cb-page-navigation')->find('li');
        $size = sizeof($page_numbers);
        if ($size > 0) {
            return $page_numbers[$size - 2]->find('a')->innerHtml;
        }
        return 0;

    }
}