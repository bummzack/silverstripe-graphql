<?php


namespace SilverStripe\GraphQL\Schema\DataObject;

use GraphQL\Type\Definition\ResolveInfo;
use SilverStripe\GraphQL\QueryHandler\SchemaContextProvider;
use SilverStripe\GraphQL\Schema\Exception\SchemaBuilderException;
use SilverStripe\GraphQL\Schema\SchemaConfig;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use Closure;
use SilverStripe\ORM\SS_List;

/**
 * Generic resolver for DataObjects
 */
class Resolver
{
    /**
     * @param DataObject $obj
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $info
     * @return array|bool|int|mixed|DataList|DataObject|DBField|SS_List|string|null
     * @throws SchemaBuilderException
     */
    public static function resolve($obj, array $args = [], array $context = [], ?ResolveInfo $info = null)
    {
        $fieldName = $info->fieldName;
        $context = SchemaContextProvider::get($context);
        $fieldName = $context->mapFieldByClassName(get_class($obj), $fieldName);
        $result = $fieldName ? FieldAccessor::singleton()->accessField($obj, $fieldName[1]) : null;
        if ($result instanceof DBField) {
            return $result->getValue();
        }

        return $result;
    }

    /**
     * Just the basic ViewableData field accessor bit, without all the property mapping
     * overhead. Useful for custom dataobject types that circumvent the model layer.
     *
     * @param DataObject $obj
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $info
     * @return array|bool|int|mixed|DataList|DataObject|DBField|SS_List|string|null
     */
    public static function baseResolve($obj, $args = [], $context = [], ?ResolveInfo $info = null)
    {
        $fieldName = $info->fieldName;
        $result = FieldAccessor::singleton()->accessField($obj, $fieldName);
        if ($result instanceof DBField) {
            return $result->getValue();
        }

        return $result;
    }

}
