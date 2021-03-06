<?php
namespace Payum\Be2Bill\Tests\Action;

use Payum\Be2Bill\Api;
use Payum\Be2Bill\Payment;
use Payum\Request\CaptureRequest;
use Payum\Be2Bill\Action\CaptureAction;
use Payum\Be2Bill\Model\PaymentDetails;

class CaptureActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldImplementActionInterface()
    {
        $rc = new \ReflectionClass('Payum\Be2Bill\Action\CaptureAction');

        $this->assertTrue($rc->implementsInterface('Payum\Action\ActionInterface'));
    }

    /**
     * @test
     */
    public function shouldImplementApiAwareInterface()
    {
        $rc = new \ReflectionClass('Payum\Be2Bill\Action\CaptureAction');

        $this->assertTrue($rc->implementsInterface('Payum\ApiAwareInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()   
    {
        new CaptureAction();
    }

    /**
     * @test
     */
    public function shouldSupportCaptureRequestWithPaymentDetailsAsModel()
    {
        $action = new CaptureAction();

        $request = new CaptureRequest(new PaymentDetails);

        $this->assertTrue($action->supports($request));
    }

    /**
     * @test
     */
    public function shouldSupportCaptureRequestWithArrayAccessAsModel()
    {
        $action = new CaptureAction();

        $request = new CaptureRequest($this->getMock('ArrayAccess'));

        $this->assertTrue($action->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportNotCaptureRequest()
    {
        $action = new CaptureAction();
        
        $request = new \stdClass();

        $this->assertFalse($action->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportCaptureRequestAndNotArrayAsModel()
    {
        $action = new CaptureAction();
        
        $request = new CaptureRequest(new \stdClass());
        
        $this->assertFalse($action->supports($request));
    }

    /**
     * @test
     * 
     * @expectedException \Payum\Exception\RequestNotSupportedException
     */
    public function throwIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $action = new CaptureAction();

        $action->execute(new \stdClass());
    }

    /**
     * @test
     * 
     * @expectedException \Payum\Request\UserInputRequiredInteractiveRequest
     */
    public function throwIfRequiredDataNotSet()
    {
        $action = new CaptureAction();

        $request = new CaptureRequest(array());

        //guard
        $this->assertTrue($action->supports($request));
        
        $action->execute($request);
    }

    /**
     * @test
     */
    public function shouldAllowSetApi()
    {
        $expectedApi = $this->createApiMock();
        
        $action = new CaptureAction();
        $action->setApi($expectedApi);
        
        $this->assertAttributeSame($expectedApi, 'api', $action);
    }

    /**
     * @test
     * 
     * @expectedException \Payum\Exception\UnsupportedApiException
     */
    public function throwIfUnsupportedApiGiven()
    {
        $action = new CaptureAction();
        
        $action->setApi(new \stdClass);
    }

    /**
     * @test
     */
    public function shouldDoNothingIfExeccodeNotNull()
    {
        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->never())
            ->method('payment')
        ;
        
        $action = new CaptureAction();
        $action->setApi($apiMock);

        $request = new CaptureRequest(array('EXECCODE' => 1));

        //guard
        $this->assertTrue($action->supports($request));

        $action->execute($request);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Api
     */
    protected function createApiMock()
    {
        return $this->getMock('Payum\Be2Bill\Api', array(), array(), '', false);
    }
}
