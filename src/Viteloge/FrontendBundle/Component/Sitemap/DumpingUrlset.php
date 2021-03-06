<?php

namespace Viteloge\FrontendBundle\Component\Sitemap {

    use Presta\SitemapBundle\Sitemap\DumpingUrlset as BaseDumpingUrlset;

    /**
     * Urlset which writes added URLs into (temporary) files directly, w/o consuming memory
     * This class exist in order to FIX to meny file open exception
     */
    class DumpingUrlset extends BaseDumpingUrlset {

        /**
         * Temporary file holding the body of the sitemap
         *
         * @var resource
         */
        private $bodyFile;

        /**
         *
         */
        protected $tmpFile;

        /**
         * Saves prepared (in a temporary file) sitemap to target dir
         * Basename of sitemap location is used (as they should always match)
         *
         * @param string $targetDir Directory where file should be saved
         * @param Boolean $gzip
         */
        public function save($targetDir, $gzip = false) {
            $this->initializeFileHandler();
            $filename = realpath($targetDir) . '/' . basename($this->getLoc());
            $sitemapFile = fopen($filename, 'w+');
            $structureXml = $this->getStructureXml();

            // since header may contain namespaces which may get added when adding URLs
            // we can't prepare the header beforehand, so here we just take it and add to the beginning of the file
            $header = substr($structureXml, 0, strpos($structureXml, 'URLS</urlset>'));
            fwrite($sitemapFile, $header);

            // append body file to sitemap file (after the header)
            fflush($this->bodyFile);
            fseek($this->bodyFile, 0);

            while (!feof($this->bodyFile)) {
                fwrite($sitemapFile, fread($this->bodyFile, 65536));
            }
            fwrite($sitemapFile, '</urlset>');

            $streamInfo = stream_get_meta_data($this->bodyFile);
            fclose($this->bodyFile);
            // removing temporary file
            unlink($streamInfo['uri']);

            if ($gzip) {
                $this->loc .= '.gz';
                $filenameGz = $filename . '.gz';
                fseek($sitemapFile, 0);
                $sitemapFileGz = gzopen($filenameGz, 'wb9');
                while (!feof($sitemapFile)) {
                    gzwrite($sitemapFileGz, fread($sitemapFile, 65536));
                }
                gzclose($sitemapFileGz);
            }

            fclose($sitemapFile);
            if ($gzip) {
                unlink($filename);
            }
        }

        /**
         * Append URL's XML (to temporary file)
         *
         * @param $urlXml
         */
        protected function appendXML($urlXml) {
            $this->initializeFileHandler();
            $this->bodyFile = fopen($this->tmpFile, 'w+');
            fwrite($this->bodyFile, $urlXml);
            fclose($this->bodyFile);
        }

        /**
         * @throws \RuntimeException
         */
        private function initializeFileHandler() {
            if (null !== $this->tmpFile) {
                return;
            }

            $this->tmpFile = tempnam(sys_get_temp_dir(), 'sitemap');
            if (false === $this->bodyFile = @fopen($this->tmpFile, 'w+')) {
                throw new \RuntimeException("Cannot create temporary file $tmpFile");
            }
        }

    }

}
