<?php

namespace Lib\Repositories;


use Cache;
use Carbon\Carbon;

use Symfony\Component\DomCrawler\Crawler;
use Campo\UserAgent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

use Exception;
/**
 * Class GooglePlayRepository
 * @package namespace Lib\Repositories;
 */
class GooglePlayRepository
{

    private $rootUrl  = 'https://play.google.com';
    private $url      = 'https://play.google.com';

    public $opts = [
        'lang'          => 'en',
        'country'       => 'us'
    ];

    private $webClient;

    /**
    * __construct()
    * Initialize our Class Here for Dependecy Injection
    *
    * @return void
    * @access  public
    **/
    public function __construct()
    {

        $this->opts['lang'] = env('SET_LOCALE');
        $this->webClient = new Client(array(
            'headers' => array('User-Agent' => UserAgent::random())
        ));
    }

    /**
    * setOptions()
    * 
    *
    * @return void
    * @access  public
    **/
    public function setOptions($opt = [])
    {
        $opts = array_merge($this->opts,$opt);
        $this->opts = $opts;
        return $this;
    }


    /**
    * search()
    * 
    * @return void
    * @access  public
    **/
    public function search($q)
    {
        
        try {
            
            $this->url .= '/store/search?c=apps&q='.$q.'&hl='.$this->opts['lang'].'&gl='.$this->opts['country'];
            $that = $this;

            $searchResults = Cache::remember( str_slug(trim($q)) , 1, function() use ($that) {

                    $response = $this->webClient->get($this->url);
                    $content = $response->getBody()->getContents();
                    $crawler = new Crawler($content);
                    
                    $apps = $crawler->filter('div.card-content')->each(function(Crawler $node,$i)  use($that) {
                        return $that->cardContent($node);
                    });
                    return $apps;

                });

            return $searchResults;

        } catch (GuzzleException $e) {
            if ($e instanceof ClientException && $e->hasResponse()) {
                throw new Exception($e->getResponse()->getReasonPhrase(), 3);
            }
            else
                throw new Exception($e->getMessage(), 3);
        }
    }


    /**
    * detail()
    *
    * @return void
    * @access  public
    **/
    public function details($appId,$opts = [],$isFrontEnd = false)
    {
        if(!$appId)
            throw new Exception("No App Url Found", 1);
        

        // if($isFrontEnd == true)
        // {
        //     $appMarket = app('Lib\Repositories\AppMarketRepositoryEloquent');
        //     $appModel = $appMarket->byAppId($appId);
        //     if($appModel)
        //         throw new Exception(sprintf('This app id [%s] is exists already in our system.',$appId), 1);
                
        // }

        $options = array_merge($this->opts,$opts);
        $params = [
            'id' => $appId,
            'hl' => $options['lang'],
            'gl' => $options['country'],
        ];
        $query = http_build_query($params);

        $detailUrl = $this->url.'/store/apps/details?'.$query;

        // $detailUrl = 'https://service.prerender.io/'.$detailUrl;

        // try {

            $that = $this;
            // $content = Cache::remember(str_slug(trim($appId)) , 1500, function() use ($that,$detailUrl,$query,$appId) {


                // classes to change.
                $singelDetailWrapper        = 'div.oQ6oV';
                $appTitleWrapper            = 'h1[itemprop="name"] span';
                $appDescriptionWrapper      = 'div[itemprop="description"] div';
                $coverImageWrapper          = 'img[itemprop="image"]';


                // additional
                $additionalContentWrapper = 'span.htlgb';
                $developerWrapper         = 'a.hrTbp';
                $categoryWrapper          = 'a[itemprop="genre"]';
                 $ratingScore             = 'div.BHMmbe';
                $ratingTotal              = 'span.EymY4b span';
                $ratingBarWrapper         = 'div.mMF0fd';

                $ratingBarNumWrapper    = 'span';
                $ratingBarLengthWrapper = 'span.L2o20d';
                $ratingBarNumberWrapper = 'span.UfW5d';
               
                $screenShotWrapper = '.NIc6yf img';
                $commentWrapper    = 'div.Boieuf';
                

                $response = $this->webClient->get($detailUrl);
                $content = $response->getBody()->getContents();

                $crawler = new Crawler($content);


                $detailInfo  = $crawler->filter($singelDetailWrapper);
                $screenShots = $crawler->filter($screenShotWrapper);
                

                $appTitle = utf8_decode(trim($detailInfo->filter($appTitleWrapper)->text()));
                
                $description = $crawler->filter($appDescriptionWrapper)->html(); 
                $description = utf8_decode($description);
                

                $coverImageUrl = $detailInfo->filter($coverImageWrapper)->attr('src');

                
                $parsed = parse_url($coverImageUrl);

                if (empty($parsed['scheme'])) {
                    $coverImageUrl = 'https://' . ltrim($coverImageUrl, '/');
                }

                
                $additionalDetails = [];
                $formattedDetails  = [];
                if($crawler->filter($additionalContentWrapper)->count() > 0)
                {
                    $additionalDetails = $crawler->filter($additionalContentWrapper)
                            ->each(function(Crawler $node,$i){
                                $lists = [];
                                $nodeText      = $node->count() > 0 ? utf8_decode(trim($node->text())) : '';
                                switch ($i) {
                                    case '0':
                                        $lists['published_date'] = $nodeText;
                                        break;
                                    case '1':
                                        $lists['file_size'] = $nodeText;
                                        break;

                                    case '2':
                                        $lists['installs'] = $nodeText;
                                        break;

                                    case '3':
                                        $lists['current_version'] = $nodeText;
                                        break;

                                    case '4':
                                        $lists['required_android'] = $nodeText;
                                        break;

                                    case '5':
                                        $contentRating = '';
                                        if($node->filter('div')->count() > 0)
                                            $contentRating = utf8_decode(trim( $node->filter('div')->first()->text() ));
                                        $lists['content_rating'] = @$contentRating;
                                        break;

                                    case '6':
                                        $lists['interactive_elements'] = $nodeText;
                                        break;

                                    case '7':
                                        $lists['in_app_products'] = $nodeText;
                                        break;

                                    case '10':
                                        $lists['offered_by'] = $nodeText;
                                        break;
                                }
                                return $lists;
                            });

                    $additionalDetails = array_filter($additionalDetails);
                    $formatDetails     = array_values($additionalDetails);

                    
                    foreach ($formatDetails as $key => $detail) {
                        $formattedDetails[key($detail)] = utf8_decode(trim( $detail[key($detail)] ) );
                    }
                }
                
                
                $ratingHistorgram = $crawler->filter($ratingBarWrapper)
                                        ->each(function(Crawler $node,$i) use($ratingBarLengthWrapper,$ratingBarNumWrapper,$ratingBarNumberWrapper) {

                                            $barLength = $node->filter($ratingBarLengthWrapper)->count() > 0 ? $node->filter($ratingBarLengthWrapper)->attr('style') : '100';
                                            // $barLength = str_replace('width: ', '', $barLength);

                                            $num = $node->filter($ratingBarNumWrapper)->count() > 0 ? $node->filter($ratingBarNumWrapper)->text() : 0;
                                            $bar_number = $node->filter($ratingBarLengthWrapper)->count() > 0 ? $node->filter($ratingBarLengthWrapper)->attr('title') : 0;
                                            return [
                                                'num'           => utf8_decode(trim($num)),
                                                'bar_length'    => utf8_decode(trim($barLength)),
                                                'bar_number'    => utf8_decode(trim($bar_number)),
                                            ];
                                        });
                 
                $commentArr = [];
                // $comments = $crawler->filter($commentWrapper)->html();
                // // pre($comments);exit;
                // if($comments->count() > 0)
                //     $commentArr = $comments->each(function(Crawler $node,$i){

                //                             $authorWrapper = 'span.js5pLc';
                //                             // $authorImg = $node->filter('.author-image')->attr('style');
                //                             // preg_match('/\(([^)]+)\)/', $authorImg, $match);
                                            
                //                             return [

                //                                 'author'         => utf8_decode($node->filter($authorWrapper)->text()),
                //                                 // 'published_date' => $node->filter('.review-date')->text(),
                //                                 // 'comments'      => utf8_decode($node->filter('div.Z8UXhc span')->text()),
                //                                 // 'image'         => @$match[1],
                //                             ];
                //                         });


                // pre($commentArr);
                // exit;
                
                $rateScore   = $crawler->filter($ratingScore)->count() > 0 ? trim($crawler->filter($ratingScore)->text()) : 0;
                $ratingTotal = $crawler->filter($ratingTotal)->count() > 0 ? trim($crawler->filter($ratingTotal)->last()->text())    : 0;


                $detailArray = [
                    'app_id'        => $appId,
                    'app_link'      => $detailUrl,
                    'title'         => $appTitle,
                    'description'   => $description,
                    'cover_image'   => $coverImageUrl,
                    'developer'  => [
                        'link' => utf8_decode(trim( @$detailInfo->filter($developerWrapper)->attr('href') )),
                        'name' => utf8_decode(trim( @$detailInfo->filter($developerWrapper)->text()) )
                    ],
                    'category' => [
                        'name' => utf8_decode(trim($detailInfo->filter($categoryWrapper)->text())),
                    ],
                    'rate_score'    => round($rateScore,1),
                    'ratings_total' => utf8_decode($ratingTotal),

                    'rating_histogram' => $ratingHistorgram,
                    'reviews'    => $commentArr,

                    'screenshots' => str_replace('2x', '', $screenShots->extract('srcset')),
                    'price' => $detailInfo->filter('meta[itemprop=price]')->attr('content'),
                ];
                
                $detailArray = array_merge($detailArray,$formattedDetails);
                // pre($detailArray);exit;
                return $detailArray;
            // });

            // return $content;

        // } catch (GuzzleException $e) {
        //     if ($e instanceof ClientException && $e->hasResponse()) {
        //         throw new Exception($e->getResponse()->getReasonPhrase(), 3);
        //     }
        //     else
        //         throw new Exception($e->getMessage(), 3);
        // }
    }


    /**
    * cardContent()
    * 
    * @return void
    * @access  private
    **/
    private function cardContent($node)
    {

        $title              = $this->hasData($node->filter('a.title')) != false ? $node->filter('a.title')->attr('title') : '';
        $description        = $this->hasData($node->filter('div.description')) != false ? trim($node->filter('div.description')->text()) : '';
        $link               = $this->hasData($node->filter('a.title')) != false ? $node->filter('a.title')->attr('href') : '';
        $developer_link     = $this->hasData($node->filter('div.subtitle-container a.subtitle')) != false ? $node->filter('div.subtitle-container a.subtitle')->attr('href') : '';

        $developer_name    = $this->hasData($node->filter('div.subtitle-container a.subtitle')) != false ? $node->filter('div.subtitle-container a.subtitle')->attr('title') : '';

        $image_l = $this->hasData($node->filter('div.cover-image-container img')) != false ? $node->filter('div.cover-image-container img')->attr('data-cover-large') : '';
        $image_m = $this->hasData($node->filter('div.cover-image-container img')) != false ? $node->filter('div.cover-image-container img')->attr('data-cover-small') : '';
        $ratings = $this->hasData($node->filter('div.tiny-star')) != false ? $node->filter('div.tiny-star')->attr('aria-label') : 0;
        $price   = $this->hasData($node->filter('span.display-price')) != false ? $node->filter('span.display-price')->text() : 'Free';

        $appId = $node->attr('data-docid');



        $parsedL = parse_url($image_l);
        if (empty($parsedL['scheme'])) {
            $image_l = 'https://' . ltrim($image_l, '/');
        }

        $parsedS = parse_url($image_m);
        if (empty($parsedS['scheme'])) {
            $image_m = 'https://' . ltrim($image_m, '/');
        }

        return [
            'title'       => utf8_decode($title),
            'description' => utf8_decode($description),
            'link'        => $this->rootUrl.$link,
            'app_id'      => $appId,
            'developer'  => [
                'link' => $this->rootUrl.$developer_link,
                'name' => utf8_decode($developer_name)
            ],
            'image' => [
                'large'  => $image_l,
                'small' => $image_m,
            ],
            'ratings' => numberInAString($ratings),
            'price'   => $price
        ];
    }

    /**
    * hasData()
    * 
    * @return void
    * @access  private
    **/
    private function hasData($filterNode)
    {
        if($filterNode->count() > 0)
            return $filterNode;
        return false;
    }

}