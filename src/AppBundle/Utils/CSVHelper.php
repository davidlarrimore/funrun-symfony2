<?php

// src/AppBundle/Utils/CSVHelper.php

namespace AppBundle\Utils;

class CSVHelper
{
    private $headers = [];
    private $data = [];
    private $fileName = null; //Includes Extension
    private $fileExtension = null;
    private $filePath = null;
    private $CSVFile = null;
    private $headerWhiteList = [];

    public function __construct()
    {
    }

    public function processFile($filePath, $fileName)
    {
        $this->setfileName($fileName);
        $this->setfilePath($filePath);

        $counter = 0;
        $thisRow;

        $this->setCSVFile(fopen($this->getFilePath().$this->getFileName(), 'r'));

        while (!feof($this->getCSVFile())) {
            $thisRow = fgetcsv($this->getCSVFile());
            if (!empty($thisRow)) {
                $thisRowAsObjects = [];
                if ($counter == 0) {
                    $this->setHeaders($thisRow);
                } else {
                    $this->addRow($thisRow);
                }
                ++$counter;
            }
        }

        fclose($this->getCSVFile());
        unlink($this->getFilePath().$this->getFileName());
    }

    public function setCSVFile($CSVFile)
    {
        $this->CSVFile = $CSVFile;
    }

    public function getCSVFile()
    {
        return $this->CSVFile;
    }

    public function setHeaderWhiteList($headerWhiteList)
    {
        $this->headerWhiteList = $headerWhiteList;
    }

    public function getHeaderWhiteList($headerWhiteList)
    {
        return $this->headerWhiteList;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setHeaders($headers)
    {
        $this->headers = $this->cleanHeaders($headers);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function addRow($rowData)
    {
        $theObject = [];
        $row = $this->cleanRow($rowData);
        foreach ($this->getHeaders() as $key => $value) {
            $theObject[$value] = $row[$key];
        }
        array_push($this->data, $theObject);
    }

    public function cleanRow($rowData)
    {
        foreach ($rowData as $key => $value) {
            $rowData[$key] = $this->cleanDataString($value);
        }

        return $rowData;
    }

    public function cleanHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $headers[$key] = $this->cleanHeaderString($value);
        }

        return $headers;
    }

    public function addHeader($header)
    {
        array_push($this->header, $this->cleanHeaders($header));
    }

    public function getRow($index)
    {
        return $this->data[$index];
    }

    /**
     * @return array of objects
     *
     * @param array of strings $base
     * @param array of objects $compare
     * @param string           $method
     */
    public function validateHeaders($compare)
    {
        $check = true;
        $makeStringArray = [];
        //first we are turning the array of objects into array of strings
        $makeStringArray = $this->createStringArray($compare);

        foreach ($this->getHeaders() as $key => $value) {
            $loopCheck = false;
            foreach ($makeStringArray as $key2 => $value2) {
                if (strcmp($value, $value2)) {
                    $loopCheck = true;
                }
            }
            if (!$loopCheck) {
                $check = false;
            }
        }

        return $check;
    }

    public function createStringArray($in)
    {
        $makeStringArray = [];

        //first we are turning the array of objects into array of strings
        foreach ($in as $key => $value) {
            array_push($makeStringArray, $value);
        }

        return $makeStringArray;
    }

    public function cleanHeaderString($string)
    {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with underscores.
        $string = preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.
        $string = trim($string);

        return strtolower($string);
    }

    public function cleanDataString($string)
    {
        $string = preg_replace('/[^A-Za-z0-9\_ -.]/', '', $string); // Removes special chars.
        $string = trim($string);

        return $string;
    }

    public function cleanTeacherNames()
    {
        $thisData = $this->getData();
        foreach ($thisData as $rowIndex => $rowData) {
            foreach ($rowData as $key => $value) {
                if (strcmp($key, 'teachers_name') == 0) {
                    $teacherNameString = substr(trim($value), strpos(trim($value), ' - ') + 3, strlen(trim($value)));
                    $thisData[$rowIndex][$key] = $teacherNameString;
                }
            }
        }

        $this->setData($thisData);
    }
}
