<?php
namespace NinjaSentry\Architect;

/**
 * NinjaSentry Architect v0.1 2017
 * 
 * Class Sync
 *
 * @package NinjaSentry\Architect
 */
final class Sync
{
    /**
     * Default file permissions
     */
    const FILE_PERMISSIONS = 0644;
    
    /**
     * Default directory permissions
     */
    const DIR_PERMISSIONS  = 0755;
    
    /**
     * Sync config
     * @var
     */
    public $config;

    /**
     * Operations log
     * @var array
     */
    private $report = [];

    /**
     * Sync constructor.
     *
     * @param array $config
     */
    public function __construct( $config = [] )
    {
        $this->config = $this->json2obj( $config );
        $this->report = [];
        
        return $this;
    }
    
    /**
     * @return $this
     * @throws \Exception
     */
    public function clone()
    {
        $this->directoryMap();
        $this->fileMap();

        return $this;
    }
    
    /**
     * @return array
     */
    public function report(){
        return $this->report;
    }
    
    /**
     * @param null $obj
     *
     * @return bool|mixed
     */
    private function json2obj( $obj = null ){
        if( $obj === null ) return false;
        return json_decode( json_encode( $obj ) );
    }

    /**
     * @param string $path
     * @return string
     */
    private function getProjectPath( $path = '' )
    {
        return $this->config->basePath
            . $this->config->projectName
            . '/' . $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getSourcePath( $path = '' )
    {
        return $this->config->basePath
            . $this->config->sources
            . '/' . $path;
    }
    
    /**
     * Create directory if not found 
     *
     * @return $this
     * @throws \Exception
     */
    private function directoryMap()
    {
        if( ! is_readable( $this->config->dirMap ) )
        {
            throw new \Exception(
                'Architect Error :: DirectoryMap() - Config file not found ( dirmap.php )'
            );
        }

        $map = require $this->config->dirMap;

        if( ! is_array( $map ) )
        {
            throw new \Exception(
                'Architect Error :: DirectoryMap() - Expects $map to be an array'
            );
        }

        foreach( $map as $directoryPath )
        {
            $directoryPath = $this->getProjectPath( $directoryPath );

            if( false === ( is_dir( $directoryPath ) ) ) {
                $this->makeDir( $directoryPath );
            }

            $this->report['directory_list'][] = $directoryPath;
        }

        return $this;
    }

    /**
     * Copy files from core app skeleton folder
     *
     * @return $this
     * @throws \Exception
     */
    private function fileMap()
    {
        if( ! is_readable( $this->config->fileMap ) )
        {
            throw new \Exception(
                'Architect Error :: fileMap() - Config file not found ( filemap.php )'
            );
        }

        $map = require $this->config->fileMap;

        if( ! is_array( $map ) )
        {
            throw new \Exception(
                'Architect Error :: fileMap() - Expecting array'
            );
        }
        
        foreach( $map as $filePath )
        {
            $source = $this->getSourcePath( $filePath );

            if( is_dir( $source ) )
            {
                $reDirator = new \RecursiveDirectoryIterator(
                    $source,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                );

                $reIterator = new \RecursiveIteratorIterator(
                    $reDirator,
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach( $reIterator as $fileInfo => $resource )
                {
                    $sourceFile = $this->forwardSlashed( 
                        $resource->getPathname() 
                    );
                    
                    $copyTo = str_replace( 
                        $this->config->sources, 
                        $this->config->projectName, 
                        $sourceFile 
                    );

                    $this->report['file_list'][] = $copyTo;

                    /**
                     * Copy directory
                     */
                    if( $resource->isDir() )
                    {
                        if( false === is_dir( $copyTo ) ) {
                            $this->makeDir( $copyTo );
                        }
                    }
                    /**
                     * Copy file
                     */
                    elseif( $resource->isFile() ) {
                        $this->copyFile( $sourceFile, $copyTo );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Normalise file paths
     * 
     * @param string $str
     *
     * @return mixed
     */
    private function forwardSlashed( $str = '' ){
        return str_replace( '\\', '/', $str );
    }
    
    /**
     * @param string $directoryPath
     *
     * @throws \Exception
     */
    private function makeDir( $directoryPath = '' )
    {
        if( false === ( @mkdir( $directoryPath, self::DIR_PERMISSIONS, true ) ) )
        {
            throw new \Exception(
                'Architect Error :: makeDir() - Unable to create destination directory ( '
                . $directoryPath
                . ' )'
            );
        }
    }

    /**
     * @param string $sourceFile
     * @param string $copyTo
     *
     * @throws \Exception
     */
    private function copyFile( $sourceFile = '' , $copyTo = '' )
    {
        if( is_dir( $sourceFile ) )
        {
            throw new \Exception(
                'Architect Error :: copyFile() - Expected source file. ' 
                . 'The source path provided is a directory ( '
                . $sourceFile
                . ' ) Copying To : ( '
                . $copyTo
                . ' )'
            );
        }
        
        if( ! is_readable( $copyTo ) )
        {
            if( false === ( @copy( $sourceFile, $copyTo ) ) )
            {
                throw new \Exception(
                    'Architect Error :: copyFile() - Unable to copy file from ( '
                    . $sourceFile
                    . ' ) to ( '
                    . $copyTo
                    . ' )'
                );
            }
        }
    }
}
