<?php

namespace Digitaliseme\Enumerations;

enum DocumentType: string
{
    case Bill = 'bill';
    case Contract = 'contract';
    case Information = 'information';
    case Invoice = 'invoice';
    case Notice = 'notice';
    case Proposal = 'proposal';
    case Reminder = 'reminder';
    case Report = 'report';
    case Request = 'request';
    case Other = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
