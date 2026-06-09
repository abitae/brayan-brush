import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ComplaintController::store
 * @see app/Http/Controllers/Api/ComplaintController.php:25
 * @route '/api/complaints'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/complaints',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ComplaintController::store
 * @see app/Http/Controllers/Api/ComplaintController.php:25
 * @route '/api/complaints'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplaintController::store
 * @see app/Http/Controllers/Api/ComplaintController.php:25
 * @route '/api/complaints'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\ComplaintController::store
 * @see app/Http/Controllers/Api/ComplaintController.php:25
 * @route '/api/complaints'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ComplaintController::store
 * @see app/Http/Controllers/Api/ComplaintController.php:25
 * @route '/api/complaints'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/complaints',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\ComplaintController::index
 * @see app/Http/Controllers/Api/ComplaintController.php:15
 * @route '/api/complaints'
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
* @see \App\Http\Controllers\Api\ComplaintController::update
 * @see app/Http/Controllers/Api/ComplaintController.php:55
 * @route '/api/complaints/{complaint}'
 */
export const update = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/complaints/{complaint}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\ComplaintController::update
 * @see app/Http/Controllers/Api/ComplaintController.php:55
 * @route '/api/complaints/{complaint}'
 */
update.url = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { complaint: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { complaint: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    complaint: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        complaint: typeof args.complaint === 'object'
                ? args.complaint.id
                : args.complaint,
                }

    return update.definition.url
            .replace('{complaint}', parsedArgs.complaint.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplaintController::update
 * @see app/Http/Controllers/Api/ComplaintController.php:55
 * @route '/api/complaints/{complaint}'
 */
update.put = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\ComplaintController::update
 * @see app/Http/Controllers/Api/ComplaintController.php:55
 * @route '/api/complaints/{complaint}'
 */
    const updateForm = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ComplaintController::update
 * @see app/Http/Controllers/Api/ComplaintController.php:55
 * @route '/api/complaints/{complaint}'
 */
        updateForm.put = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\ComplaintController::destroy
 * @see app/Http/Controllers/Api/ComplaintController.php:67
 * @route '/api/complaints/{complaint}'
 */
export const destroy = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/complaints/{complaint}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ComplaintController::destroy
 * @see app/Http/Controllers/Api/ComplaintController.php:67
 * @route '/api/complaints/{complaint}'
 */
destroy.url = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { complaint: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { complaint: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    complaint: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        complaint: typeof args.complaint === 'object'
                ? args.complaint.id
                : args.complaint,
                }

    return destroy.definition.url
            .replace('{complaint}', parsedArgs.complaint.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplaintController::destroy
 * @see app/Http/Controllers/Api/ComplaintController.php:67
 * @route '/api/complaints/{complaint}'
 */
destroy.delete = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Api\ComplaintController::destroy
 * @see app/Http/Controllers/Api/ComplaintController.php:67
 * @route '/api/complaints/{complaint}'
 */
    const destroyForm = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\ComplaintController::destroy
 * @see app/Http/Controllers/Api/ComplaintController.php:67
 * @route '/api/complaints/{complaint}'
 */
        destroyForm.delete = (args: { complaint: number | { id: number } } | [complaint: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const ComplaintController = { store, index, update, destroy }

export default ComplaintController