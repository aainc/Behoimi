<?php
/**
 * Date: 15/10/13
 * Time: 19:23.
 */
namespace Behoimi\Pager;

use Hoimi\Request;

class PagingUrl
{
    private $request = null;
    private $baseUrl = null;
    /**
     * @var PagingResult
     */
    private $pagingResult = null;

    public function __construct(Request $request, PagingResult $pagingResult)
    {
        if ($request->getHeader('REQUEST_URI')) {
            $urlElement = $request->parseUrl();
            $host = $request->getHeader('SERVER_NAME');
            $scheme = $request->getHeader('HTTPS') === 'on' ? 'https' : 'http';
            $query = null;
            if (isset($urlElement['query'])) {
                parse_str($urlElement['query'], $temp);
                if ($temp) {
                    unset($temp['page'], $temp['limit'], $temp['order'], $temp['direction']);
                    if ($temp) {
                        $query = http_build_query($temp);
                    }
                }
            }
            $this->baseUrl = "$scheme://$host".$urlElement['path'].($query ? ('?'.$query) : '');
        }
        $this->request = $request;
        $this->pagingResult = $pagingResult;
    }

    public function getNext()
    {
        return $this->buildUrl($this->pagingResult->getNext());
    }

    public function getPrev()
    {
        return $this->buildUrl($this->pagingResult->getPrev());
    }

    private function buildUrl($page)
    {
        if ($page === null) {
            return;
        }

        return $this->baseUrl.(strpos($this->baseUrl, '?') === false ? '?' : '&').http_build_query(array(
            'page' => $page,
            'limit' => $this->pagingResult->getCount(),
            'order' => $this->pagingResult->getOrder(),
            'direction' => $this->pagingResult->getDirection(),
        ));
    }
}
