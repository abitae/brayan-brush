import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
const ServiciosController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ServiciosController.url(options),
    method: 'get',
})

ServiciosController.definition = {
    methods: ["get","head"],
    url: '/servicios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
ServiciosController.url = (options?: RouteQueryOptions) => {
    return ServiciosController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
ServiciosController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ServiciosController.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
ServiciosController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ServiciosController.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
    const ServiciosControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ServiciosController.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
        ServiciosControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ServiciosController.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BrayanBrush\ServiciosController::__invoke
 * @see app/Http/Controllers/BrayanBrush/ServiciosController.php:12
 * @route '/servicios'
 */
        ServiciosControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ServiciosController.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ServiciosController.form = ServiciosControllerForm
export default ServiciosController