<?php
declare(strict_types = 1);

namespace App\Repositories\Server;

use App\DataTransferObjects\Server;
use App\Models\Category\EloquentCategory;
use App\Models\Server\EloquentServer;
use App\Models\Server\ServerInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Traits\ContainerTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EloquentServerRepository
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 * @package App\Repositories\Server
 */
class EloquentServerRepository implements ServerRepositoryInterface
{
    use ContainerTrait;

    public function create(ServerInterface $entity): ServerInterface
    {
        return EloquentServer::create(trim_nullable([
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'enabled' => $entity->isEnabled(),
            'ip' => $entity->getIp(),
            'port' => $entity->getPort(),
            'password' => $entity->getPassword(),
            'monitoring_enabled' => $entity->isMonitoringEnabled()
        ]));
    }

    public function update(int $serverId, ServerInterface $entity): bool
    {
        return (bool)EloquentServer::where('id', $serverId)->update([
            'name' => $entity->getName(),
            'enabled' => $entity->isEnabled(),
            'ip' => $entity->getIp(),
            'port' => $entity->getPort(),
            'password' => $entity->getPassword(),
            'monitoring_enabled' => $entity->isMonitoringEnabled()
        ]);
    }

    public function find(int $id, array $columns)
    {
        return EloquentServer::find($id, $columns);
    }

    public function all(array $columns): iterable
    {
        return EloquentServer::all($columns);
    }

    public function getWithCategories(array $columns): iterable
    {
        $servers = EloquentServer::select($columns)
            ->with([
                'categories' => function ($query) {
                    /** @var Builder $query */
                    return $query->select(['*']);
                }
            ])
            ->get();

        return $servers;
    }

    public function allWithCategories(array $serverColumns, array $categoryColumns): iterable
    {
        return EloquentServer::select(array_merge($serverColumns, ['servers.id']))
            ->with([
                'categories' => function ($query) use ($categoryColumns) {
                    /** @var Builder $query */
                    $query->select(array_merge($categoryColumns, ['categories.server_id']));
                }
            ])
            ->get();
    }

    public function categories(int $serverId, array $columns): iterable
    {
        if (is_array($serverId)) {
            $builder = EloquentCategory::select($columns)->whereIn('server_id', $serverId);
        } else {
            $builder = EloquentCategory::select($columns)->where('server_id', $serverId);
        }

        return $builder->get();
    }

    public function enable(int $serverId): bool
    {
        return $this->changeEnabledServerMode($serverId, true);
    }

    public function disable(int $serverId): bool
    {
        return $this->changeEnabledServerMode($serverId, false);
    }

    public function count(): int
    {
        return EloquentServer::count('id');
    }

    private function changeEnabledServerMode(int $id, bool $mode): bool
    {
        return (bool)EloquentServer::where('id', $id)->update(['enabled' => $mode]);
    }

    public function delete(int $serverId): bool
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->make(ProductRepositoryInterface::class);
        $productRepository->deleteByServer($serverId);

        return (bool)EloquentServer::where('id', $serverId)->delete();
    }

    public function truncate(): void
    {
        EloquentServer::truncate();
    }
}
