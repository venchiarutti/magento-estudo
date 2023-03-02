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
use Estudo\TesteGraphQL\Model\Service\UpdateCustomerDataService;

/**
 * Resolver to update customer data
 */
class UpdateCustomer implements ResolverInterface
{
    private CustomerRepositoryInterface $customerRepository;
    private CustomerDataMapper $customerDataMapper;
    private UpdateCustomerDataService $updateCustomerDataService;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerDataMapper $customerDataMapper
     * @param UpdateCustomerDataService $updateCustomerDataService
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper,
        UpdateCustomerDataService $updateCustomerDataService
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerDataMapper = $customerDataMapper;
        $this->updateCustomerDataService = $updateCustomerDataService;
    }

    /**
     * Resolver to update customer data
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
            $customer = $this->updateCustomerDataService->updateCustomerData(
                $this->customerRepository->getById($customerId),
                $args['input']
            );

            return $this->customerDataMapper->map($customer);
        } catch (\Exception $exception) {
            throw new GraphQlInputException(__($exception->getMessage()), $exception);
        }
    }
}
