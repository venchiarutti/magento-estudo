<?php
declare(strict_types=1);

namespace Estudo\TesteGraphQL\Model\Service;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Class to update customer data
 */
class UpdateCustomerDataService
{
    /**
     * @var string
     */
    public const FIRSTNAME_ARG = 'firstname';

    /**
     * @var string
     */
    public const DOB_ARG = 'dob';

    private CustomerRepositoryInterface $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Update customer data
     *
     * @param CustomerInterface $customer
     * @param array $data
     * @return CustomerInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws InputMismatchException
     */
    public function updateCustomerData(CustomerInterface $customer, array $data): CustomerInterface
    {
        if ($data[self::DOB_ARG]) {
            $customer->setDob($data[self::DOB_ARG]);
        }

        if ($data[self::FIRSTNAME_ARG]) {
            $customer->setFirstname($data[self::FIRSTNAME_ARG]);
        }

        return $this->customerRepository->save($customer);
    }
}
