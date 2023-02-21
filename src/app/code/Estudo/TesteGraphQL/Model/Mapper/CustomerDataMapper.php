<?php
declare(strict_types=1);

namespace Estudo\TesteGraphQL\Model\Mapper;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class to map customer data
 */
class CustomerDataMapper
{
    /**
     * @var string
     */
    public const EMAIL_ARG = 'email';

    /**
     * @var string
     */
    public const NAME_ARG = 'name';

    /**
     * @var string
     */
    public const DOB_ARG = 'dob';

    /**
     * Map customer data to an array
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function map(CustomerInterface $customer): array
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $customer->getDob() ?? "");

        return [
            self::EMAIL_ARG => $customer->getEmail(),
            self::NAME_ARG => $customer->getFirstname() . " " .  $customer->getLastname(),
            self::DOB_ARG => $dateTime ? $dateTime->format('d/m/Y') : "Usuário não possui DOB cadastrada"
        ];
    }
}
