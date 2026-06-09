import ConfigController from './ConfigController'
import ServiceController from './ServiceController'
import AgencyController from './AgencyController'
import ProhibitedCategoryController from './ProhibitedCategoryController'
import PricingRouteController from './PricingRouteController'
import CalculatorCityController from './CalculatorCityController'
import TrackingController from './TrackingController'
import AssistantController from './AssistantController'
import QuoteController from './QuoteController'
import ComplaintController from './ComplaintController'
import ProhibitedItemController from './ProhibitedItemController'
const Api = {
    ConfigController: Object.assign(ConfigController, ConfigController),
ServiceController: Object.assign(ServiceController, ServiceController),
AgencyController: Object.assign(AgencyController, AgencyController),
ProhibitedCategoryController: Object.assign(ProhibitedCategoryController, ProhibitedCategoryController),
PricingRouteController: Object.assign(PricingRouteController, PricingRouteController),
CalculatorCityController: Object.assign(CalculatorCityController, CalculatorCityController),
TrackingController: Object.assign(TrackingController, TrackingController),
AssistantController: Object.assign(AssistantController, AssistantController),
QuoteController: Object.assign(QuoteController, QuoteController),
ComplaintController: Object.assign(ComplaintController, ComplaintController),
ProhibitedItemController: Object.assign(ProhibitedItemController, ProhibitedItemController),
}

export default Api