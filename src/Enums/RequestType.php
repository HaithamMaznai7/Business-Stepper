<?php

namespace haimaz\BusinessSteper\Enums;

enum RequestType: string
{
    case Customer = 'Customer';
    case Company = 'Company';
    case Partner = 'Partner';
    case B2C = 'B2C';
    case B2B = 'B2B';
    case B2Partner = 'B2Partner';

    public function label(): string
    {
      return $this->value . ' Type';
    }

    public function isPOS(): bool
    {
      return in_array($this, [self::B2C, self::B2B, self::B2Partner]);
    }

    public function businessType(): string
    {
      if(in_array($this, [self::B2Partner, self::Partner])){
        return 'partner';
      }

      if(in_array($this, [self::B2B, self::Company])){
        return 'b2b';
      }

      return 'b2c';
    }

    public function value(): string
    {
      return strtolower($this->value);
    }

    public function id(): string
    {
      return $this->value;
    }
    static public function set($string): self
    {
      $string = strtolower($string);

      switch ($string) {
        case 'customer':
          return self::Customer;
        case 'company':
          return self::Company;
        case 'partner':
          return self::Partner;
        case 'b2partner':
          return self::B2Partner;
        case 'b2b':
          return self::B2B;
        default :
          return self::B2C;
      }
    }
    
    static public function all(): array
    {
      return [
        self::Customer,
        self::Company,
        self::Partner,
        self::B2C,
        self::B2B,
        self::B2Partner,
      ];
    }
}
