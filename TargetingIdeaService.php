<?php
    /**
    * Class to produce the SOAP XML for a fixed TargetingIdeaService report.
    * This should be done with SoapClient, but honestly - I am not smart enough
    * to handle a horrible technology such as SOAP. But I can handle XML, and so
    * this works fine for me.
    *
    * TODO
    * - Create more parameters
    * - Exception handling
    * - Constructor / private instance members
    * - Replace some hardcoded values
    */
    class TargetingIdeaService {
        /**
        * Assembles the complete XML needed for keyword search volume request
        * as defined by internal standards, so parameters are reduced to a
        * minimum that works
        *
        * @param string $developerToken AdWords API developer token
        * @param string $userAgent User agent identifier
        * @return string The XML as string
        */
        function getXml($developerToken, $userAgent, $keywords) {
            // Main elements
            $dom = new DomDocument('1.0', 'UTF-8');
            $envelope = $this->getEnvelope($dom);
            $header = $this->getHeader($dom, $developerToken, $userAgent);
            $body = $dom->createElement('soapenv:Body');
            $get = $dom->createElement('v20:get');

            // Selector children
            $locationSearchParameter = $this->getLocationSearchParameter($dom);
            $languageSearchParameter = $this->getLanguageSearchParameter($dom);
            $relatedToQuerySearchParameter = $this->getRelatedToQuerySearchParameter($dom, $keywords);
            $ideaType = $dom->createElement('v20:ideaType', 'KEYWORD');
            $requestType = $dom->createElement('v20:requestType', 'STATS');
            $requestedAttributeTypes_1 = $dom->createElement('v20:requestedAttributeTypes', 'KEYWORD_TEXT');
            $requestedAttributeTypes_2 = $dom->createElement('v20:requestedAttributeTypes', 'SEARCH_VOLUME');

            // Paging
            $paging = $dom->createElement('v20:paging');
            $startIndex = $dom->createElement('v201:startIndex', '0');
            $numberResults = $dom->createElement('v201:numberResults', '100');
            $paging->appendChild($startIndex);
            $paging->appendChild($numberResults);

            // Selector assembly
            $selector = $dom->createElement('v20:selector');
            $selector->appendChild($locationSearchParameter);
            $selector->appendChild($languageSearchParameter);
            $selector->appendChild($relatedToQuerySearchParameter);
            $selector->appendChild($ideaType);
            $selector->appendChild($requestType);
            $selector->appendChild($requestedAttributeTypes_1);
            $selector->appendChild($requestedAttributeTypes_2);
            $selector->appendChild($paging);

            // Total assembly
            $get->appendChild($selector);
            $body->appendChild($get);
            $envelope->appendChild($header);
            $envelope->appendChild($body);
            $dom->appendChild($envelope);

            return $dom->saveXML();
        }

        /**
        * Creates the <soapenv:Envelope> element
        *
        * @param object $dom The main DOMDocument object
        * @return object DOMElement
        */
        private function getEnvelope($dom) {
            $envelope = $dom->createElement('soapenv:Envelope');
            $envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $envelope->setAttribute('xmlns:v20', 'https://adwords.google.com/api/adwords/o/v201306');
            $envelope->setAttribute('xmlns:v201', 'https://adwords.google.com/api/adwords/cm/v201306');
            $envelope->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            return $envelope;
        }

        /**
        * Creates the <soapenv:Header> element
        *
        * @param object $dom The main DOMDocument object
        * @param string $developerToken AdWords API developer token
        * @param string $userAgent User agent identifier
        * @return object DOMElement
        */
        private function getHeader($dom, $developerToken, $userAgent) {
            $header = $dom->createElement('soapenv:Header');
            $requestHeader = $dom->createElement('v20:RequestHeader');
            $developerToken = $dom->createElement('v201:developerToken', $developerToken);
            $userAgent = $dom->createElement('v201:userAgent', $userAgent);
            $requestHeader->appendChild($developerToken);
            $requestHeader->appendChild($userAgent);
            $header->appendChild($requestHeader);
            return $header;
        }

        private function getLocationSearchParameter($dom) {
            $locationSearchParameter = $dom->createElement('v20:searchParameters');
            $locationSearchParameter->setAttribute('xsi:type', 'v20:LocationSearchParameter');
            $locations = $dom->createElement('v20:locations');
            $id = $dom->createElement('v201:id', '2276');
            $locations->appendChild($id);
            $locationSearchParameter->appendChild($locations);
            return $locationSearchParameter;
        }

        private function getLanguageSearchParameter($dom) {
            $languageSearchParameter = $dom->createElement('v20:searchParameters');
            $languageSearchParameter->setAttribute('xsi:type', 'v20:LanguageSearchParameter');
            $languages = $dom->createElement('v20:languages');
            $id = $dom->createElement('v201:id', '1001');
            $languages->appendChild($id);
            $languageSearchParameter->appendChild($languages);
            return $languageSearchParameter;
        }

        private function getRelatedToQuerySearchParameter($dom, $keywords) {
            $relatedToQuerySearchParameter = $dom->createElement('v20:searchParameters');
            $relatedToQuerySearchParameter->setAttribute('xsi:type', 'v20:RelatedToQuerySearchParameter');
            foreach($keywords as $keyword) {
                $relatedToQuerySearchParameter->appendChild($dom->createElement('v20:queries', $keyword));
            }   
            return $relatedToQuerySearchParameter;
        }
    }
?>
