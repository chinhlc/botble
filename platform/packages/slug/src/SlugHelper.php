<?php

namespace Botble\Slug;

use Illuminate\Support\Arr;

class SlugHelper
{
    /**
     * @param string | array $model
     * @return $this
     */
    public function registerModule($model): self
    {
        if (!is_array($model)) {
            $model = [$model];
        }
        config([
            'packages.slug.general.supported' => array_merge(config('packages.slug.general.supported', []), $model),
        ]);

        return $this;
    }

    /**
     * @param string $model
     * @param string|null $prefix
     * @return $this
     */
    public function setPrefix(string $model, ?string $prefix): self
    {
        $prefixes = config('packages.slug.general.prefixes', []);
        $prefixes[$model] = $prefix;

        config(['packages.slug.general.prefixes' => $prefixes]);

        return $this;
    }

    /**
     * @param string $model
     * @return string|null
     */
    public function getPrefix(string $model): ?string
    {
        return Arr::get(config('packages.slug.general.prefixes', []), $model, '');
    }

    /**
     * @return array
     */
    public function supportedModels()
    {
        return config('packages.slug.general.supported', []);
    }

    /**
     * @return array
     */
    public function isSupportedModel(string $model): bool
    {
        return in_array($model, $this->supportedModels());
    }
}
