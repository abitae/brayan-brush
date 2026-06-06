import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/calculator-cities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\CalculatorCityController::index
 * @see app/Http/Controllers/Api/CalculatorCityController.php:12
 * @route '/api/calculator-cities'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Api\CalculatorCityController::store
 * @see app/Http/Controllers/Api/CalculatorCityController.php:17
 * @route '/api/calculator-cities'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/calculator-cities',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::store
 * @see app/Http/Controllers/Api/CalculatorCityController.php:17
 * @route '/api/calculator-cities'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::store
 * @see app/Http/Controllers/Api/CalculatorCityController.php:17
 * @route '/api/calculator-cities'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\CalculatorCityController::store
 * @see app/Http/Controllers/Api/CalculatorCityController.php:17
 * @route '/api/calculator-cities'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\CalculatorCityController::store
 * @see app/Http/Controllers/Api/CalculatorCityController.php:17
 * @route '/api/calculator-cities'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\CalculatorCityController::update
 * @see app/Http/Controllers/Api/CalculatorCityController.php:38
 * @route '/api/calculator-cities/{calculatorCity}'
 */
export const update = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/calculator-cities/{calculatorCity}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::update
 * @see app/Http/Controllers/Api/CalculatorCityController.php:38
 * @route '/api/calculator-cities/{calculatorCity}'
 */
update.url = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { calculatorCity: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { calculatorCity: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    calculatorCity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        calculatorCity: typeof args.calculatorCity === 'object'
                ? args.calculatorCity.id
                : args.calculatorCity,
                }

    return update.definition.url
            .replace('{calculatorCity}', parsedArgs.calculatorCity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::update
 * @see app/Http/Controllers/Api/CalculatorCityController.php:38
 * @route '/api/calculator-cities/{calculatorCity}'
 */
update.put = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\CalculatorCityController::update
 * @see app/Http/Controllers/Api/CalculatorCityController.php:38
 * @route '/api/calculator-cities/{calculatorCity}'
 */
    const updateForm = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\CalculatorCityController::update
 * @see app/Http/Controllers/Api/CalculatorCityController.php:38
 * @route '/api/calculator-cities/{calculatorCity}'
 */
        updateForm.put = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\Api\CalculatorCityController::destroy
 * @see app/Http/Controllers/Api/CalculatorCityController.php:58
 * @route '/api/calculator-cities/{calculatorCity}'
 */
export const destroy = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/calculator-cities/{calculatorCity}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::destroy
 * @see app/Http/Controllers/Api/CalculatorCityController.php:58
 * @route '/api/calculator-cities/{calculatorCity}'
 */
destroy.url = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { calculatorCity: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { calculatorCity: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    calculatorCity: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        calculatorCity: typeof args.calculatorCity === 'object'
                ? args.calculatorCity.id
                : args.calculatorCity,
                }

    return destroy.definition.url
            .replace('{calculatorCity}', parsedArgs.calculatorCity.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CalculatorCityController::destroy
 * @see app/Http/Controllers/Api/CalculatorCityController.php:58
 * @route '/api/calculator-cities/{calculatorCity}'
 */
destroy.delete = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Api\CalculatorCityController::destroy
 * @see app/Http/Controllers/Api/CalculatorCityController.php:58
 * @route '/api/calculator-cities/{calculatorCity}'
 */
    const destroyForm = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\CalculatorCityController::destroy
 * @see app/Http/Controllers/Api/CalculatorCityController.php:58
 * @route '/api/calculator-cities/{calculatorCity}'
 */
        destroyForm.delete = (args: { calculatorCity: number | { id: number } } | [calculatorCity: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const CalculatorCityController = { index, store, update, destroy }

export default CalculatorCityController