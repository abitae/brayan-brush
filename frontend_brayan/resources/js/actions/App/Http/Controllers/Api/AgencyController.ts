import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/agencies',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\AgencyController::index
 * @see app/Http/Controllers/Api/AgencyController.php:12
 * @route '/api/agencies'
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
* @see \App\Http\Controllers\Api\AgencyController::store
 * @see app/Http/Controllers/Api/AgencyController.php:17
 * @route '/api/agencies'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/agencies',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AgencyController::store
 * @see app/Http/Controllers/Api/AgencyController.php:17
 * @route '/api/agencies'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgencyController::store
 * @see app/Http/Controllers/Api/AgencyController.php:17
 * @route '/api/agencies'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\AgencyController::store
 * @see app/Http/Controllers/Api/AgencyController.php:17
 * @route '/api/agencies'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\AgencyController::store
 * @see app/Http/Controllers/Api/AgencyController.php:17
 * @route '/api/agencies'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\AgencyController::update
 * @see app/Http/Controllers/Api/AgencyController.php:38
 * @route '/api/agencies/{agency}'
 */
export const update = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/agencies/{agency}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\AgencyController::update
 * @see app/Http/Controllers/Api/AgencyController.php:38
 * @route '/api/agencies/{agency}'
 */
update.url = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { agency: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { agency: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    agency: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        agency: typeof args.agency === 'object'
                ? args.agency.id
                : args.agency,
                }

    return update.definition.url
            .replace('{agency}', parsedArgs.agency.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgencyController::update
 * @see app/Http/Controllers/Api/AgencyController.php:38
 * @route '/api/agencies/{agency}'
 */
update.put = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\AgencyController::update
 * @see app/Http/Controllers/Api/AgencyController.php:38
 * @route '/api/agencies/{agency}'
 */
    const updateForm = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\AgencyController::update
 * @see app/Http/Controllers/Api/AgencyController.php:38
 * @route '/api/agencies/{agency}'
 */
        updateForm.put = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\AgencyController::destroy
 * @see app/Http/Controllers/Api/AgencyController.php:56
 * @route '/api/agencies/{agency}'
 */
export const destroy = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/agencies/{agency}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AgencyController::destroy
 * @see app/Http/Controllers/Api/AgencyController.php:56
 * @route '/api/agencies/{agency}'
 */
destroy.url = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { agency: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { agency: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    agency: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        agency: typeof args.agency === 'object'
                ? args.agency.id
                : args.agency,
                }

    return destroy.definition.url
            .replace('{agency}', parsedArgs.agency.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AgencyController::destroy
 * @see app/Http/Controllers/Api/AgencyController.php:56
 * @route '/api/agencies/{agency}'
 */
destroy.delete = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Api\AgencyController::destroy
 * @see app/Http/Controllers/Api/AgencyController.php:56
 * @route '/api/agencies/{agency}'
 */
    const destroyForm = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\AgencyController::destroy
 * @see app/Http/Controllers/Api/AgencyController.php:56
 * @route '/api/agencies/{agency}'
 */
        destroyForm.delete = (args: { agency: number | { id: number } } | [agency: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const AgencyController = { index, store, update, destroy }

export default AgencyController