<?php
/**
 * File containing the ezcSearchRstXmlExtractor class.
 *
 * @package Search
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class extracts title and body from a parsed RST file in XML format.
 *
 * @package Search
 * @version //autogentag//
 */
class ezcSearchRstXmlExtractor /* implements ezcSearchExtractor */
{
    /**
     * Extracts information from the file $fileName associated with the url $url.
     *
     * @param string $fileName
     * @param string $url
     * @return array(ezcSearchDocument)
     */
    static public function extract( $fileName, $type, $url, $imagePath = null, $imageUrlPath = null )
    {
        $published = filemtime( $fileName );

        $converted = file_get_contents( $fileName );
        $dom = new DomDocument();
        @$dom->loadHtml( $converted );
        $tbody = $dom->getElementsByTagName('div')->item(0);

        $xpath = new DOMXPath($dom);
        $tocElem = $xpath->evaluate("//h1[@class='title']", $tbody )->item(0);
        $title = $tocElem ? $tocElem->nodeValue : 'no title';

        $docs = array();
        $body = $urls = array();
        $currentUrl = $url;
        $lastUrl = $url;
        $currentBody = '';

        // child::*[self::p or self::h1]
        $xpath = new DOMXPath($dom);
//        $tbody = $xpath->evaluate("descendant::*[self::p or self::h1 or self::dl or self::img or self::a]", $tbody );
        $tbody = $xpath->evaluate("//p|//h1|//ol|//ul|//dl|//img|//a", $tbody );
//        $tbody = $dom->getElementsByTagName('p');
        $body = '';
        foreach( $tbody as $item )
        {
            switch ( $item->tagName )
            {
                case 'a':
                        $name = $item->getAttribute( 'name' );
//                        echo "[a] ", $name, "\n";
                        if ( strlen( $name ) )
                        {
                            $currentUrl = $url . '#'. $name;
                        }
                    break;
                case 'img':
                        $alt = $item->getAttribute( 'alt' );
                        $src = $item->getAttribute( 'src' );
                        $location = $imagePath == null ?
                            (dirname( $fileName ). '/'. $src) : 
                            ($imagePath. '/'. preg_replace( '@(\.\./)+@', '', $src ) );
                        $imgurl = $src[0] == '/' ?
                            $src :
                            ($imageUrlPath === null ?
                                ($url . '/' . $src) :
                                ($imageUrlPath. '/'. preg_replace( '@(\.\./)+@', '', $src ) ) );
                        echo "  - $src => $imgurl\n";
                        $docs[] = self::extractImage( $alt, $location, $imgurl );
                    break;
                case 'p':
                case 'h1':
                case 'dl':
                        if ( $lastUrl !== $currentUrl )
                        {
                            $docs[] = new ezcSearchSimpleArticle( null, $title, $currentBody, $published, $lastUrl, $type );
                            $currentBody = '';
                            $lastUrl = $currentUrl;
                        }
                        $currentBody .= strip_tags( $dom->saveXml( $item ) ) . "\n\n";
                    break;
            }
        }
        if ( $currentBody != '' )
        {
            $docs[] = new ezcSearchSimpleArticle( null, $title, $currentBody, $published, $lastUrl, $type );
        }

//        $docs[] = new ezcSearchSimpleArticle( null, $title, $body, $published, $urls, $type );
        return $docs;
    }

    private static function extractImage( $title, $filename, $url )
    {
        $info = getimagesize( $filename );

        return new ezcSearchSimpleImage( null, $title, $url, $info[0], $info[1], $info['mime'], $url );
    }
}
?>
