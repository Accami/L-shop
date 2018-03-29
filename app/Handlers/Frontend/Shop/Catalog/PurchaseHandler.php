<?php
declare(strict_types = 1);

namespace App\Handlers\Frontend\Shop\Catalog;

use App\DataTransferObjects\Frontend\Shop\Purchase;
use App\Entity\Distribution;
use App\Exceptions\Product\DoesNotExistException;
use App\Repository\Distribution\DistributionRepository;
use App\Repository\Product\ProductRepository;
use App\Services\Auth\Auth;
use App\Services\Purchasing\Distributors\Distributor;
use App\Services\Purchasing\PurchaseCreator;

class PurchaseHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var DistributionRepository
     */
    private $distributionRepository;

    /**
     * @var PurchaseCreator
     */
    private $creator;

    /**
     * @var Distributor
     */
    private $distributor;

    public function __construct(
        Auth $auth,
        ProductRepository $productRepository,
        DistributionRepository $distributionRepository,
        PurchaseCreator $creator,
        Distributor $distributor)
    {
        $this->auth = $auth;
        $this->productRepository = $productRepository;
        $this->distributionRepository = $distributionRepository;
        $this->creator = $creator;
        $this->distributor = $distributor;
    }

    public function handle(int $productId, int $amount, ?string $username, string $ip)
    {
        $product = $this->productRepository->find($productId);
        if ($product === null) {
            throw new DoesNotExistException($productId);
        }
        if ($this->auth->check()) {
            $user = $this->auth->getUser();
        } else {
            $user = $username;
        }

        $purchase = new Purchase($product, $amount);
        $purchase = $this->creator->create([$purchase], $user, $ip);

        foreach ($purchase->getItems() as $purchaseItem) {
            $distribution = new Distribution($purchaseItem);
            if ($purchase->isCompleted()) {
                $this->distributor->distribute($distribution);
            } else {
                //
            }
        }
    }
}