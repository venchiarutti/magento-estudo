<?php
declare(strict_types=1);

namespace Estudo\TesteGraphQL\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Estudo\TesteGraphQL\Model\Mapper\CustomerDataMapper;

/**
 * Resolver to return customer data
 */
class GetCustomerData implements ResolverInterface
{
    /**
     * @var string
     */
    public const CUSTOMER_ID = 'customerId';

    private CustomerRepositoryInterface $customerRepository;
    private CustomerDataMapper $customerDataMapper;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerDataMapper $customerDataMapper
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerDataMapper = $customerDataMapper;
    }

    /**
     * Resolver to return customer data
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     * @throws GraphQlAuthorizationException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        $customerId = $context->getUserId();

        if (!$customerId) {
            throw new GraphQlAuthorizationException(__('Usuário não está logado.'));
        }

        try {
            $customer = $this->customerRepository->getById($customerId);

            return $this->customerDataMapper->map($customer);
        } catch (\Exception $exception) {
            throw new GraphQlInputException(__($exception->getMessage()), $exception);
        }
    }
}
