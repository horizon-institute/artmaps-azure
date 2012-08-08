//
//  POIAnnotationView.m
//  WordPress
//
//  Created by Shakir Ali on 13/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "OoIAnnotationView.h"

int const OOIANNOTATION_LABEL = 1;
int const DETAIL_OoIMETA_BTN_TAG = 2;

@implementation OoIAnnotationView

//@synthesize map;

- (id)initWithAnnotation:(id <MKAnnotation>)annotation reuseIdentifier:annotationID{
    self = [super initWithAnnotation:annotation reuseIdentifier:annotationID];
    if (self){
        self.draggable = YES;
        self.canShowCallout = YES;
        [self addLeftCalloutAccessoryView];
        [self addRightCalloutAccessoryView];
    }
    return self;
}

-(void)addLeftCalloutAccessoryView{
    CGRect frameRect = CGRectMake(0,0,200,20);
    UIView *leftCAView = [[UIView alloc] initWithFrame:frameRect];
    leftCAView.backgroundColor = nil;
    UILabel* label = [[UILabel alloc] initWithFrame:frameRect];
    label.font = [UIFont boldSystemFontOfSize:14];
    label.textColor = [UIColor whiteColor];
    label.backgroundColor = nil;
    label.opaque = NO;
    label.tag = OOIANNOTATION_LABEL;
    [leftCAView addSubview:label];
    [label release];
    self.leftCalloutAccessoryView = leftCAView;
    [leftCAView release];
}

-(void)addRightCalloutAccessoryView{
    self.rightCalloutAccessoryView = [UIButton buttonWithType:UIButtonTypeDetailDisclosure];
    [self.rightCalloutAccessoryView setTag:DETAIL_OoIMETA_BTN_TAG];
}

-(void)dealloc{
    //map = nil;
    [super dealloc];
}

@end
