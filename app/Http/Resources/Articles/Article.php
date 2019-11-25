<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Articles\Article as ArticleModel;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Http\Resources
 * @mixin \App\Models\Articles\Article
 */
class Article extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request) {
        $type = $this->additional['meta']['type'] ?? ArticleModel::ARTICLES_TYPE_DEFAULT;
        $responseType = $this->additional['meta']['responseType'] ?? ArticleModel::ARTICLES_RESPONSE_TYPE_SIMPLE_ACTUAL;

        $article = [
            'id'        => $this->getKey(),
            'name'      => $this->getName(),
            'mainImage' => $this->getCoverLink(),
        ];

        if ($responseType === ArticleModel::ARTICLES_RESPONSE_TYPE_SIMPLE_ACTUAL || $type === ArticleModel::ARTICLES_TYPE_DEFAULT || $responseType === ArticleModel::ARTICLES_RESPONSE_TYPE_BOOKMARKS) {
            $article['short_description'] = $this->getShortDescription();
            $article['author'] = $this->getAuthor();
            $article['organization'] = $this->getOrganizationInfo();
            $article['readTime'] = $this->getReadTime();
            $article['views'] = $this->getViews();
            $article['is_actual'] = $this->isActual();
            $article['label'] = [
                'src'  => $this->getArticleLabelLink(),
                'name' => $this->getArticleName(),
            ];
            $article['categories'] = $this->getCategoriesShortInfo();
            $article['nextArticle'] = $this->getNextArticle();
        }

        if ($type === ArticleModel::ARTICLES_TYPE_DEFAULT) {
            $article['content'] = $this->getText();
        }

        return $article;
    }
}
