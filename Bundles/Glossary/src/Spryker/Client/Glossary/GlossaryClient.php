<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Client\Glossary;

use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Spryker\Client\Glossary\GlossaryFactory getFactory()
 */
class GlossaryClient extends AbstractClient implements GlossaryClientInterface
{

    /**
     * @param string $id
     * @param string $localeName
     * @param array $parameters
     *
     * @return string
     */
    public function translate($id, $localeName, array $parameters = [])
    {
        return $this->createTranslator($localeName)->translate($id, $parameters);
    }

    /**
     * @param string $localeName
     *
     * @return \Spryker\Client\Glossary\Storage\GlossaryStorageInterface
     */
    protected function createTranslator($localeName)
    {
        return $this->getFactory()->createCachedTranslator($localeName);
    }

}
