<?php
declare(strict_types = 1);

namespace App\Repositories\Payment;

use App\DataTransferObjects\Payment;
use App\Models\Payment\EloquentPayment;
use App\Models\Payment\PaymentInterface;
use Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EloquentPaymentRepository
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 * @package App\Repositories\Payment
 */
class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function create(PaymentInterface $entity): PaymentInterface
    {
        return EloquentPayment::create([
            'service' => $entity->getService(),
            'products' => $entity->getProductsAsString(),
            'cost' => $entity->getCost(),
            'user_id' => $entity->getUserId(),
            'username' => $entity->getUsername(),
            'server_id' => $entity->getServerId(),
            'ip' => $entity->getIp(),
            'completed' => $entity->isCompleted()
        ]);
    }

    public function find(int $id, array $columns): ?PaymentInterface
    {
        return EloquentPayment::find($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function forTheLastYearCompleted(array $columns): iterable
    {
        return Cache::get('admin.statistic.for_the_last_year_completed', function () use ($columns) {
            $result = EloquentPayment::select($columns)
                ->where('updated_at', '>', '(NOW() - INTERVAL 1 YEAR)')
                ->where('completed', 1)
                ->orderBy('updated_at', 'ASC')
                ->get();

            Cache::put('admin.statistic.for_the_last_year_completed', $result, s_get('caching.statistic.ttl', 60));

            return $result;
        });
    }

    public function profit(): float
    {
        return Cache::get('admin.statistic.profit', function () {
            $result = EloquentPayment::where('completed', 1)
                ->where(function ($query) {
                    /** @var $query Builder */
                    $query->where('username', null)
                        ->orWhere(function ($query) {
                            /** @var $query Builder */
                            $query->whereNotNull('username')
                                ->where('products', null);
                        });
                })
                ->sum('cost');

            Cache::put('admin.statistic.profit', $result, s_get('caching.statistic.ttl', 60));

            return $result;
        });
    }

    public function complete(int $id, string $serviceName): bool
    {
        return (bool)EloquentPayment::where('id', $id)
            ->update([
                'service' => $serviceName,
                'completed' => true
            ]);
    }

    public function historyForUser(int $userId, array $columns): LengthAwarePaginator
    {
        return EloquentPayment::select($columns)
            ->where('payments.user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->paginate(s_get('profile.payments_per_page', 10));
    }

    public function withUserPaginated(array $paymentColumns, array $userColumns): LengthAwarePaginator
    {
        return EloquentPayment::select(array_merge($paymentColumns, ['user_id']))
            ->with([
                'user' => function ($query) use ($userColumns) {
                    /** @var Builder $query */
                    $query->select(array_merge($userColumns, ['id']));
                }
            ])
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
    }

    public function delete(int $id): bool
    {
        return (bool)EloquentPayment::where('id', $id)->delete();
    }

    public function deleteByUserId(int $userId): bool
    {
        return (bool)EloquentPayment::where('user_id', $userId)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function truncate(): void
    {
        EloquentPayment::truncate();
    }
}
