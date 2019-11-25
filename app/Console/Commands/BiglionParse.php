<?php

namespace App\Console\Commands;

use App\Models\Biglion;
use App\Models\Cities\City;
use App\Models\Communications\Email;
use App\Models\Communications\Phone;
use App\Models\Files\Image;
use App\Models\Articles\Article;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationPointSchedule;
use App\Models\Organizations\Point;
use App\Models\Products\Category;
use App\Models\Products\Product;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class BiglionParse extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'go-parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'parsing biglion';

    /**
     * @var
     */
    protected $client;

    /**
     * @var
     */
    protected $dadataClient;

    /**
     * @var array
     */
    protected $linksForParse = [
        41 => [ //Ссылки категории "Обучение"
                '/services/education/',
                '/services/education/?page=2',
        ],
        45 => [ //Ссылки категории "Продукты"
                '/services/goods/foods/',
        ],
        48 => [ //Ссылки категории "Здоровье"
                '/services/health/',
                '/services/health/?page=2',
        ],
        49 => [ //Ссылки категории "Детям"
                '/services/children/',
                '/services/children/?page=2',
        ],
        50 => [ //Ссылки категории "Фитнес"
                '/services/fitness/',
        ],
        51 => [ //Ссылки категории "Развлечения"
                '/services/entertainment/',
                '/services/entertainment/?page=2',
                '/services/entertainment/?page=3',
        ],
        52 => [ //Ссылки категории "Разное"
                '/services/other/',
                '/services/other/?page=2',
                '/services/other/?page=3',
        ],
        53 => [ //Ссылки категории "Концерты"
                '/services/concert/',
        ],
        54 => [ //Ссылки категории "Авто"
                '/services/auto/',
        ],
        55 => [ //Ссылки категории "Кафе"
                '/services/restaurant/',
                '/services/restaurant/?page=2',
        ],
        56 => [ //Ссылки категории "Красота"
                '/services/beauty/',
                '/services/beauty/?page=2',
                '/services/beauty/?page=3',
                '/services/beauty/?page=4',
                '/services/beauty/?page=5',
        ],
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param Biglion $biglion
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Biglion $biglion) {
        //foreach ($this->linksForParse as $categoryId => $linksForParse) {
        //    foreach ($linksForParse as $linkForParse) {
        //        dump($categoryId . ' - ' . $linkForParse);
        //        Log::debug('message: ' . $categoryId . ' - ' . $linkForParse);
        //        $client = $this->getClient();
        //        $htmlProducts = $client->get($linkForParse)->getBody()->getContents();
        //        $crawler = new Crawler($htmlProducts);
        //        $filterProducts = '.card-item.js-card > .card-item__box';
        //        $productCards = $crawler->filter($filterProducts)->each(function (Crawler $node) {
        //            return $node->attr('href');
        //        });
        //        unset($crawler);
        //
        //        foreach ($productCards as $productCard) {
        //            dump('| ==== ' . $productCard);
        //            $biglion = Biglion::firstOrCreate([
        //                'link' => $productCard
        //            ], [
        //                'link' => $productCard
        //            ]);
        //
        //            if (!$biglion->wasRecentlyCreated) {
        //                dump('| ==== skip');
        //                continue;
        //            }
        //
        //            $_organization = [];
        //            $productHtml = $client->get($productCard)->getBody()->getContents();
        //
        //            $crawler = new Crawler($productHtml);
        //            $filterOrganizationInform = '.page__deal-offer.js-init';
        //            $organizationInform = $crawler->filter($filterOrganizationInform)->each(function (Crawler $node) {
        //                return json_decode($node->attr('data-props'), true);
        //            });
        //            unset($crawler);
        //            Log::debug('$organizationInform', (array)$organizationInform);
        //            //dd(12312323);
        //
        //            //dump('| ========= $organizationInform: ');
        //            //dump($organizationInform);
        //
        //            if (isset($organizationInform[0]['partner']['legalInfo'][0]) && strlen($organizationInform[0]['partner']['legalInfo'][0]['phone']) !== 0) {
        //                $fullPhone = explode(';', $organizationInform[0]['partner']['legalInfo'][0]['phone'])[0];
        //                $phoneWithoutCode = null;
        //                $codePhone = null;
        //
        //                try {
        //                    $phoneNumber = explode(' ', $fullPhone);
        //                    preg_match('/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/', $phoneNumber[1], $matches);
        //                    $phoneWithoutCode = $matches[0];
        //                    $codePhone = substr($phoneNumber[0], 1);
        //                } catch (\Exception $exception) {
        //                    Log::error('point@store parsing phone', [
        //                        'message' => $exception->getMessage(),
        //                        'line'    => $exception->getLine(),
        //                        'code'    => $exception->getCode()
        //                    ]);
        //                }
        //
        //                //if (false !== strpos($phone, '+7')) {
        //                //    $phone = str_replace(['+7', ' ', '(', ')', '-'], ['', '', '', '', ''], $phone);
        //                //
        //                //    if (substr($phone, 0, 2) === '78' || substr($phone, 0, 2) === '88') {
        //                //        $phone = substr($phone, 1);
        //                //    }
        //                //} elseif (substr($phone, 0, 1) === '8') {
        //                //    $phone = str_replace([' ', '(', ')', '-'], ['', '', '', ''], $phone);
        //                //    $phone = substr($phone, 1);
        //                //} elseif (substr($phone, 0, 1) === '7') {
        //                //    $phone = str_replace([' ', '(', ')', '-'], ['', '', '', ''], $phone);
        //                //    $phone = substr($phone, 1);
        //                //}
        //
        //                //if (strlen($phone) > 10) {
        //                //    $phone = '';
        //                //}
        //            } else {
        //                $fullPhone = null;
        //            }
        //
        //            $_organization['name'] = $organizationInform[0]['partner']['title'];
        //            $_organization['inn'] = $organizationInform[0]['partner']['legalInfo'][0]['inn'] ?? null;
        //            $_organization['orgnip'] = $organizationInform[0]['partner']['legalInfo'][0]['ogrn'] ?? null;
        //            $_organization['full_phone'] = $fullPhone;
        //            $_organization['phoneWithoutCode'] = $phoneWithoutCode;
        //            $_organization['codePhone'] = $codePhone;
        //            $_organization['email'] = $organizationInform[0]['partner']['legalInfo'][0]['email'] ?? null;
        //            $_organization['link'] = $organizationInform[0]['partner']['url'] ?? null;
        //            Log::debug('$_organization', (array)$_organization);
        //            //dump('| ========= $_organization: ');
        //            //dump($_organization);
        //            $organization = Organization::firstOrCreate([
        //                'name' => $_organization['name'],
        //                'inn'  => $_organization['inn']
        //            ], $_organization);
        //            $dadataClient = $this->getDadataClient();
        //
        //            if (!$organization->wasRecentlyCreated) {
        //                $organization->setLink($_organization['link']);
        //            }
        //
        //            if (!is_null($_organization['inn'])) {
        //                $guzzleOrg = $dadataClient->request('GET', '/suggestions/api/4_1/rs/suggest/party', [
        //                    'headers' => [
        //                        'Content-Type'  => 'application/json',
        //                        'Accept'        => 'application/json',
        //                        'Authorization' => 'Token 1824220a034db4dce4b2bdd0d23282b53c6c1c49',
        //                    ],
        //                    'query'   => [
        //                        'query' => $_organization['inn']
        //                    ],
        //                ])->getBody()->getContents();
        //                $guzzleOrg = json_decode($guzzleOrg, true);
        //                Log::debug('$guzzleOrg', (array)$guzzleOrg);
        //
        //                if (isset($guzzleOrg['suggestions'][0])) {
        //                    $payload = [
        //                        'orgnip'    => $guzzleOrg['suggestions'][0]['data']['ogrn'],
        //                        'okved'     => $guzzleOrg['suggestions'][0]['data']['okved'],
        //                        'address'   => $guzzleOrg['suggestions'][0]['data']['address']['unrestricted_value'],
        //                        'latitude'  => $guzzleOrg['suggestions'][0]['data']['address']['data']['geo_lat'],
        //                        'longitude' => $guzzleOrg['suggestions'][0]['data']['address']['data']['geo_lon']
        //                    ];
        //                    $organization->setPayload($payload);
        //                }
        //            }
        //
        //            if (isset($_organization['phone']) && is_null($organization->getPhone())) {
        //                $phone = $organization->phones()->save(new Phone([
        //                    'full_phone' => $_organization['full_phone'],
        //                    'code' => $_organization['codePhone'],
        //                    'phone' => $_organization['phoneWithoutCode']
        //                ]));
        //                $organization->phone()->associate($phone);
        //            }
        //
        //            if (isset($_organization['email']) && is_null($organization->getEmail())) {
        //                $email = $organization->emails()->save(new Email(['email' => $_organization['email']]));
        //                $organization->email()->associate($email);
        //            }
        //
        //            if (isset($organizationInform[0]['partner']['brandInternalLink'])) {
        //                $organizationMainPageHtml = $client->get($organizationInform[0]['partner']['brandInternalLink'])
        //                                                   ->getBody()
        //                                                   ->getContents();
        //
        //                $crawler = new Crawler($organizationMainPageHtml);
        //                $filterOrganizationAvatar = '.brand_header > img.brand_img';
        //                $organizationAvatarLink = $crawler->filter($filterOrganizationAvatar)
        //                                                  ->each(function (Crawler $node) {
        //                                                      return $node->attr('src');
        //                                                  });
        //                unset($crawler);
        //                if (count($organizationAvatarLink) > 0) {
        //                    if (is_null($organization->getAvatar())) {
        //                        $avatar = new Image();
        //                        $file = 'https:' . $organizationAvatarLink[0];
        //
        //                        if (Image::isSVG($file)) {
        //                            if (!Image::isBase64($file)) {
        //                                $file = Image::getBase64WithMimeType($file);
        //                            }
        //
        //                            $avatar->downloadSVG($file);
        //                        } else {
        //                            $avatar->download($file);
        //                        }
        //
        //                        //$avatar->download('https:' . $organizationAvatarLink[0]);
        //                        $avatar = $organization->images()->save($avatar);
        //                        $organization->avatar()->associate($avatar);
        //                    }
        //                }
        //            }
        //            $organization->save();
        //
        //            $pointsForProduct = [];
        //            foreach ($organizationInform[0]['map']['placemarks'] as $point) {
        //                $guzzlePoint = $dadataClient->request('GET', '/suggestions/api/4_1/rs/suggest/address', [
        //                    'headers' => [
        //                        'Content-Type'  => 'application/json',
        //                        'Accept'        => 'application/json',
        //                        'Authorization' => 'Token 1824220a034db4dce4b2bdd0d23282b53c6c1c49',
        //                    ],
        //                    'query'   => [
        //                        'query' => $point['title']
        //                    ],
        //                ])->getBody()->getContents();
        //                $guzzlePoint = json_decode($guzzlePoint, true);
        //                Log::debug('$guzzlePoint', (array)$guzzlePoint);
        //
        //                if (isset($guzzlePoint['suggestions'][0])){
        //                    $fullStret = $guzzlePoint['suggestions'][0]['value'];
        //                    $latitude = ($guzzlePoint['suggestions'][0]['data']['geo_lat']) ? Str::substr($guzzlePoint['suggestions'][0]['data']['geo_lat'], 0, 9) : null;
        //                    $latitudeFull = $guzzlePoint['suggestions'][0]['data']['geo_lat'] ?? null;
        //                    $longitude = ($guzzlePoint['suggestions'][0]['data']['geo_lon']) ? Str::substr($guzzlePoint['suggestions'][0]['data']['geo_lon'], 0, 9) : null;
        //                    $longitudeFull = $guzzlePoint['suggestions'][0]['data']['geo_lon'] ?? null;
        //                    $street = $guzzlePoint['suggestions'][0]['data']['street_with_type'];
        //                    $building = $guzzlePoint['suggestions'][0]['data']['house'];
        //                } else {
        //                    $fullStret = '';
        //                    $latitude = null;
        //                    $latitudeFull = null;
        //                    $longitude = null;
        //                    $longitudeFull = null;
        //                    $street = '';
        //                    $building = '';
        //                }
        //
        //                /**
        //                 * @var Point $point
        //                 */
        //                $point = $organization->points()->firstOrCreate([
        //                    'name'        => $_organization['name'],
        //                    'full_street' => $fullStret,
        //                    'latitude'    => $latitude,
        //                    'longitude'   => $longitude,
        //                    'street'      => $street,
        //                    'building'    => $building
        //                ], [
        //                    'name'        => $_organization['name'],
        //                    'full_street' => $fullStret,
        //                    'latitude'    => $latitudeFull,
        //                    'longitude'   => $longitudeFull,
        //                    'street'      => $street,
        //                    'building'    => $building
        //                ]);
        //
        //                if (!$point->wasRecentlyCreated) {
        //                    $point->setLatitude($latitude);
        //                    $point->setLongitude($longitude);
        //                }
        //
        //                if (isset($point['PHONE']) && strlen($point['PHONE']) > 0) {
        //                    $phone = $point->phones()->save(new Phone(['phone' => $point['PHONE']]));
        //                    $point->phone()->associate($phone);
        //                }
        //                $point->save();
        //                $pointsForProduct[] = $point->getKey();
        //            }
        //
        //            $crawler = new Crawler($productHtml);
        //            $filterDiscountOfProduct = '.deal-offer__main > .deal-offer__discount b';
        //            $discountOfProduct = $crawler->filter($filterDiscountOfProduct)->each(function (Crawler $node) {
        //                return str_replace(['- ', '%'], ['', ''], $node->text());
        //            });
        //            $filterOldPriceOfProduce = '.deal-offer__main .price .price--old';
        //            $oldPriceOfProduct = $crawler->filter($filterOldPriceOfProduce)->each(function (Crawler $node) {
        //                return str_replace(['от', 'руб.', ' '], ['', '', ''], $node->text());
        //            });
        //            $filterConditionsOfProduct = '.deal-offer__main .info .tabs__items div[data-tab=0]';
        //            $conditionsOfProduct = $crawler->filter($filterConditionsOfProduct)->each(function (Crawler $node) {
        //                return $node->html();
        //            });
        //            $filterDescriptionOfProduct = '.deal-offer__main .info .tabs__items div[data-tab=2] .info__text';
        //            $descriptionOfProduct = $crawler->filter($filterDescriptionOfProduct)
        //                                            ->each(function (Crawler $node) {
        //                                                return $node->text();
        //                                            });
        //            /**
        //             * @var Product $product
        //             */
        //            $_startAt = substr($organizationInform[0]['product']['date']['start'], 0, 8);
        //            $_endAt = substr($organizationInform[0]['product']['date']['end'], 0, 8);
        //            $startAt = substr($_startAt, 0, 4) . '-' . substr($_startAt, 4, 2) . '-' . substr($_startAt, 6, 2);
        //            $endAt = substr($_endAt, 0, 4) . '-' . substr($_endAt, 4, 2) . '-' . substr($_endAt, 6, 2);
        //            $productName = mb_strimwidth($organizationInform[0]['product']['title'], 0, 250, "...");
        //            $product = $organization->products()->firstOrCreate([
        //                'name' => $productName,
        //            ], [
        //                'name'         => $productName,
        //                'value'        => (isset($discountOfProduct[0]))
        //                    ? $discountOfProduct[0]
        //                    : 0,
        //                'origin_price' => (isset($oldPriceOfProduct[0]))
        //                    ? $oldPriceOfProduct[0]
        //                    : 0,
        //                'start_at'     => $startAt,
        //                'end_at'       => $endAt,
        //                'conditions'   => (isset($conditionsOfProduct[0]))
        //                    ? $conditionsOfProduct[0]
        //                    : '',
        //                'description'  => (isset($descriptionOfProduct[0]))
        //                    ? $descriptionOfProduct[0]
        //                    : '',
        //            ]);
        //
        //            if (!$product->wasRecentlyCreated) {
        //                $product->setStartAt(\Illuminate\Support\Carbon::createFromDate($startAt));
        //                $product->setEndAt(\Illuminate\Support\Carbon::createFromDate($endAt));
        //            }
        //
        //            $product->points()->attach($pointsForProduct);
        //            $product->categories()->attach($categoryId);
        //
        //            if (!$product->wasRecentlyCreated) {
        //                $images = $product->getImages();
        //
        //                foreach ($images as $image) {
        //                    /**
        //                     * @var Image $image
        //                     */
        //                    $image->forceDelete();
        //                }
        //            }
        //
        //            if (isset($organizationInform[0]['product']['photo']['big']) && count($organizationInform[0]['product']['photo']['big']) !== 0) {
        //                $savedImages = 0;
        //                foreach ($organizationInform[0]['product']['photo']['big'] as $link) {
        //                    if ($savedImages === 7) {
        //                        break;
        //                    }
        //
        //                    $file = 'https:' . $link;
        //
        //                    if ($savedImages === 0) {
        //                        $miniature = new Image();
        //                        if (Image::isSVG($file)) {
        //                            if (!Image::isBase64($file)) {
        //                                $file = Image::getBase64WithMimeType($file);
        //                            }
        //
        //                            $miniature->downloadSVG($file, 'miniature_');
        //                        } else {
        //                            $miniature->download($file, 'miniature_', null, 'jpg', 10);
        //                        }
        //                        $miniature = $product->images()->save($miniature);
        //                        $product->miniature()->associate($miniature);
        //                    }
        //
        //                    $image = new Image();
        //
        //                    if (Image::isSVG($file)) {
        //                        if (!Image::isBase64($file)) {
        //                            $file = Image::getBase64WithMimeType($file);
        //                        }
        //
        //                        $image->downloadSVG($file, 'product_image_');
        //                        $product->images()->save($image);
        //                    } else {
        //                        $image->setMaxWidth(765);
        //                        $image->setMaxHeight(null);
        //                        $image->download($file, 'product_image_765');
        //                        $product->images()->save($image);
        //
        //                        $widths = [580, 480];
        //                        foreach ($widths as $width) {
        //                            $imageLess = new Image();
        //                            $imageLess->setMaxWidth($width);
        //                            $imageLess->setMaxHeight(null);
        //                            $imageLess->download($file, 'product_image_' . $width);
        //                            $imageLess->parent()->associate($image);
        //                            $imageLess->save();
        //                            $product->images()->save($imageLess);
        //                        }
        //                    }
        //
        //                    //$image->download($file);
        //                    //$product->images()->save($image);
        //                    $savedImages++;
        //                }
        //            }
        //
        //            $product->save();
        //        }
        //    }
        //}
    }

    /**
     * @return Client
     */
    public function getClient(): Client {
        if (is_null($this->client)) {
            $this->client = new Client([
                'base_uri' => 'https://speterburg.biglion.ru',
                'timeout'  => 30,
            ]);
        }

        return $this->client;
    }

    /**
     * @return Client
     */
    public function getDadataClient(): Client {
        if (is_null($this->dadataClient)) {
            $this->dadataClient = new Client([
                'base_uri' => 'https://suggestions.dadata.ru',
                'timeout'  => 30,
            ]);
        }

        return $this->dadataClient;
    }
}
