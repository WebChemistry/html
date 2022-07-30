<?php

namespace WebChemistry\Html\Visitor\Action;

enum EnterAction: int
{

	case DONT_TRAVERSE_CHILDREN = 1;
	case STOP_TRAVERSAL = 2;

}
