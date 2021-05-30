<?php

namespace Omatech\Editora\Domain\CmsData;

class PublishingInfo
{
    private string $status;
    private ?int $startPublishingDate;
    private ?int $endPublishingDate;

    public function __construct(string $status='O', $startPublishingDate=null, $endPublishingDate=null)
    {
        $this->setStatus($status);
        $this->setPublishingDates($startPublishingDate, $endPublishingDate);
    }

    public static function fromArray(array $arr): PublishingInfo
    {
        $status=(isset($arr['metadata']['status']))?$arr['metadata']['status']:'O';
        $startPublishingDate=(isset($arr['metadata']['startPublishingDate']))?$arr['metadata']['startPublishingDate']:null;
        $endPublishingDate=(isset($arr['metadata']['endPublishingDate']))?$arr['metadata']['endPublishingDate']:null;
        return new PublishingInfo($status, $startPublishingDate, $endPublishingDate);
    }

    public function toArray()
    {
        $ret=[];
        $ret['status']=$this->status;

        if (!empty($this->startPublishingDate)) {
            $ret['startPublishingDate']=$this->startPublishingDate;
        }

        if (!empty($this->endPublishingDate)) {
            $ret['endPublishingDate']=$this->endPublishingDate;
        }

        return $ret;
    }

    private function validateStatus($status)
    {
        if ($status!='P' && $status!='V' && $status!='O') {
            throw new \Exception("Incorrect status $status for instance. Valid values are (O)k, (V)erified, (P)ending");
        }
    }

    public function setStatus($status)
    {
        $this->validateStatus($status);
        $this->status=$status;
    }

    public function isPublished($time=null)
    {
        if ($this->status=='P'||$this->status=='V') {
            return false;
        }

        // POST Cond: Status==O
        if ($time==null) {
            $time=time();
        }

        return ($this->getStartPublishingDateOr0()<=$time
        && $this->getEndPublishingDateOr3000()>=$time);
    }

    public function setStartPublishingDate($time=null)
    {
        if ($time!==null && $this->getEndPublishingDateOr3000()<=$time) {
            throw new \Exception("Cannot set start publication date after end publication date");
        }
        $this->startPublishingDate=$time;
    }

    public function setEndPublishingDate($time=null)
    {
        if ($time!==null && $this->getStartPublishingDateOr0()>=$time) {
            throw new \Exception("Cannot set end publication date before start publication date");
        }
        $this->endPublishingDate=$time;
    }

    public function setPublishingDates($startDate=null, $endDate=null)
    {
        if ($startDate!==null && $endDate!==null) {
            if ($startDate>$endDate) {
                throw new \Exception("Cannot set end publication date before start publication date");
            }
        }
        $this->startPublishingDate=$startDate;
        $this->endPublishingDate=$endDate;
    }

    private function getEndPublishingDateOr3000()
    {
        $endDate=32512176000; // year 3.000
        if ($this->endPublishingDate) {
            $endDate=$this->endPublishingDate;
        }
        return $endDate;
    }

    private function getStartPublishingDateOr0()
    {
        $startDate=0;
        if ($this->startPublishingDate) {
            $startDate=$this->startPublishingDate;
        }
        return $startDate;
    }
}
